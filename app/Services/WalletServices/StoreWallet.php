<?php
namespace App\Services\WalletServices;

use App\Exceptions\InsufficientFund;
use App\Exceptions\InvalidTransactionHistory;
use App\Models\OrderFundLock;
use App\Models\StoreWallet as StoreWalletModel;
use App\Models\WithdrawalRequest;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\TransactionDetails;
use App\Traits\HasEncryption;
use App\Traits\TokenGenerator;
use Illuminate\Support\Facades\DB;

class StoreWallet extends WalletService {
    use HasEncryption,TokenGenerator,TokenGenerator;
    protected $store_id;
    protected $locked_fund_ids;
    public function __construct($store_id){
       parent::__construct();
       $this->store_id = $store_id;
    }

    protected function getLockedFundIds($new = true){
        if($new === false && isset($this->locked_fund_ids)){
            return $this->locked_fund_ids;
        } else {
            $this->locked_fund_ids = OrderFundLock::where('store_id',$this->store_id)
            ->where('status',OrderFundLock::STATUS_LOCKED)
            ->pluck('wallet_fund_id');
            return $this->locked_fund_ids;
        }
    }

    public function getTotalUnLockedCredits(){
        $total_credits = 0;
        $locked_ids = $this->getLockedFundIds();
        $credits = StoreWalletModel::where('store_id',$this->store_id)
        ->where('ledger_type',StoreWalletModel::LEDGER_CREDIT)
        ->whereNotIn('id',$locked_ids)->select('id','amount')->get();
        if(count($credits) > 0){
            foreach($credits as $credit){
                $total_credits += $credit->amount;
            }
        }
        return $total_credits;
    }

    public function createWithdrawalRef(){
        while(true){
            $ref_number = $this->createNumberToken(12);
            $reference = "wref-".$ref_number;
            if(!WithdrawalRequest::where('reference',$reference)->exists()){
                return $reference;
            }
        }
    }

    public function createWithdrawalRequest($amount,$bank_account_id)
    {
        $transactions = DB::table('store_wallets')->where('store_id',$this->store_id)->get();
        if(!$this->historyIsValid($transactions)){
            throw new InvalidTransactionHistory("Sorry your transaction history is invalid.");
        }
        if($amount > $this->getTotalAccountBalance()){
            throw new InsufficientFund("You do not have sufficient funds to fill the withdrawal request for the specified amount.");
        }
        WithdrawalRequest::create([
            'store_id' => $this->store_id,
            'user_id' => $this->user->id,
            'status' => WithdrawalRequest::STATUS_PENDING,
            'bank_account_id' => $bank_account_id,
            'reference' => $this->createWithdrawalRef(),
            'amount' => $amount
        ]);
        return true;

    }

    public function getTotalDebits(){
        $total_debits = 0;
        $debits = StoreWalletModel::where('store_id',$this->store_id)
        ->where('ledger_type',StoreWalletModel::LEDGER_DEBIT)
        ->select('id','amount')->get();
        if(count($debits) > 0){
            foreach($debits as $debit){
                $total_debits += $debit->amount;
            }
        }
        return $total_debits;
    }

    

    public function getTotalLockedCredits(){
       $locked_ids = $this->getLockedFundIds();
       $total = 0;
       $credits = StoreWalletModel::where('store_id',$this->store_id)
       ->where('ledger_type',StoreWalletModel::LEDGER_CREDIT)
       ->whereIn('id',$locked_ids)->select('id','amount')->get();
       foreach($credits as $credit){
           $total += $credit->amount;
       }
       return $total;
    }

    public function getTotalPendingWithdrawal(){
        $requests = WithdrawalRequest::where('store_id',$this->store_id)
            ->where('status',WithdrawalRequest::STATUS_PENDING)->select('id','amount')->get();
        $total = 0;
        if(count($requests) > 0){
            foreach($requests as $request){
                $total += $request->amount;
            }
        }
        return $total;
    }

    public function getTotalAccountBalance(){
        $ledger_balance = $this->getTotalUnLockedCredits() - $this->getTotalDebits();
        $balance = $ledger_balance - $this->getTotalPendingWithdrawal();
        return $balance;
    }

    protected function createOrderFundLock( $fund,LockDetails $lock_details,string $lock_pass){
        return OrderFundLock::create([
            'user_id' => $fund->sender_id,
            'store_id' => $this->store_id,
            'order_id' => $lock_details->order_id,
            'sub_order_id' => $lock_details->sub_order_id,
            'lock_password' => $this->encryptData($lock_pass,$fund->sender_id),
            'wallet_fund_id' => $fund->id,
            'status' => OrderFundLock::STATUS_LOCKED
        ]);
    }

    public function debitWallet($amount,$trx_details = null){
        $previous_row = $this->getPreviousRow();
        $data = [
            'store_id' => $this->store_id,
            'previous_row_hash' => $this->generateHashFromRow($previous_row),
            'amount' => $amount,
            'ledger_type' => StoreWalletModel::LEDGER_DEBIT
        ];
        if(isset($trx_details)){
            $data['transaction_id'] = $trx_details->transaction->id;
            $data['transaction_type'] = $trx_details->transaction_type;
        }
        $fund = $this->createLedgerRecord($data,$previous_row);
        return $fund;
    }

    protected function getPreviousRow(){
        return DB::table("store_wallets")->where('store_id',$this->store_id)
        ->orderBy('id','desc')->first();
    }

    protected function createLedgerRecord($data,$previous_row){
        $fund = null;
        DB::transaction(function()use($data,&$fund,$previous_row){
            StoreWalletModel::create($data);
            $fund = $this->getPreviousRow();
            //$hashable_fund = $this->convertRowAmount($fund);
            if(isset($previous_row)){
                DB::table('store_wallets')
                ->where('id',$previous_row->id)
                ->update([
                    'next_row_hash' => $this->generateHashFromRow($fund)
                ]);

            }
        });
        return $fund;
    }

    public function depositLockedOrderFund(
        $amount, SenderObject $sender, 
        LockDetails $lock_details,TransactionDetails $trx_details = null){
        $previous_row = $this->getPreviousRow();
        $data = [
            'store_id' => $this->store_id,
            'previous_row_hash' => $this->generateHashFromRow($previous_row),
            'amount' => $amount, 'sender_id' => $sender->sender_id,
            'sender_type' => $sender->sender_type, 'ledger_type' => StoreWalletModel::LEDGER_CREDIT
        ];
        if(isset($trx_details)){
            $data['transaction_id'] = $trx_details->transaction->id;
            $data['transaction_type'] = $trx_details->transaction_type;
        }
        $fund = $this->createLedgerRecord($data,$previous_row);
        $fund_pass = $this->generatePassword();
        $lock = $this->createOrderFundLock($fund,$lock_details,$fund_pass);
        return json_decode(json_encode(['fund_password' => $fund_pass,'lock_model'=>$lock]));
    }

    public function getCurrentMonthCredits(){
        $current_date = now();
        $current_month = $current_date->month;
        $current_year = $current_date->year;
        $locked_fund_ids = $this->getLockedFundIds(false);
        $total_credits = 0;
        $data = StoreWalletModel::where('store_id',$this->store_id)
        ->whereMonth('created_at',$current_month)
        ->whereYear('created_at',$current_year)
        ->whereNotIn('id',$locked_fund_ids)
        ->where('ledger_type',StoreWalletModel::LEDGER_CREDIT)->get();
        if(count($data) > 0){
            foreach($data as $data_item){
                $total_credits += $data_item->amount;
            }
        }
        return $total_credits;
    }

    public function getCurrentDayCredits(){
        $current_date = now();
        $locked_fund_ids = $this->getLockedFundIds(false);
        $formatted_date = $current_date->toDateString();
        $total_credits = 0;
        $data = StoreWalletModel::where('store_id',$this->store_id)
        ->whereDate('created_at',$formatted_date)
        ->whereNotIn('id',$locked_fund_ids)
        ->where('ledger_type',StoreWalletModel::LEDGER_CREDIT)->get();
        if(count($data) > 0){
            foreach($data as $data_item){
                $total_credits += $data_item->amount;
            }
        }
        return $total_credits;
    }

    public function getCurrentYearCredits(){
        $current_date = now();
        $current_year = $current_date->year;
        $locked_fund_ids = $this->getLockedFundIds(false);
        $total_credits = 0;
        $data = StoreWalletModel::where('store_id',$this->store_id)
        ->whereYear('created_at',$current_year)
        ->whereNotIn('id',$locked_fund_ids)
        ->where('ledger_type',StoreWalletModel::LEDGER_CREDIT)->get();
        if(count($data) > 0){
            foreach($data as $data_item){
                $total_credits += $data_item->amount;
            }
        }
        return $total_credits;
    }

}
    