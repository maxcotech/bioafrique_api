<?php
namespace App\Services\WalletServices;

use App\Models\OrderCommissionLock;
use App\Models\SuperAdminWallet as SuperAdminWalletModel;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\TransactionDetails;
use App\Models\WalletModel;
use App\Services\WalletServices\Utilities\TransferRecipient;
use App\Traits\HasArrayOperations;
use App\Traits\HasRateConversion;
use Illuminate\Support\Facades\DB;

class SuperAdminWallet extends WalletService{

    use HasArrayOperations,HasRateConversion;
    public function __construct(){
        parent::__construct();
    }

    public function canDebitAccount($amount){
        $transactions = DB::table('super_admin_wallet')->get();
        if(!$this->historyIsValid($transactions)){
            throw new \Exception('Sorry, your transaction history is not valid.');
        } else {
            $balance = $this->getTotalAccountBalance();
            if($balance >= $amount){
                return true;
            } else {
                throw new \Exception('Insufficient funds.');
            }
        }
        return false;
    }

    public function withdrawFund($amount)
    {
        $fund = null;
        if($this->canDebitAccount($amount)){
            $previous_row = $this->getPreviousRow();
            //$hashable_prev_row = $this->convertRowAmount($previous_row);
            $data = [
                'amount' => $amount,
                'previous_row_hash' => $this->generateHashFromRow($previous_row),
                'ledger_type' => SuperAdminWalletModel::LEDGER_DEBIT,
            ];
            $fund = $this->createLedgerRecord($data,$previous_row);
        }
        return $fund;
    }

    public function transferFund($amount, TransferRecipient $recipient)
    {
        $fund = null;
        if($this->canDebitAccount($amount)){
            $previous_row = $this->getPreviousRow();
            $data = [
                'amount' => $amount,
                'previous_row_hash' => $this->generateHashFromRow($previous_row),
                'ledger_type' => SuperAdminWalletModel::LEDGER_DEBIT,
            ];
            DB::transaction(function ()use($data,&$fund,$previous_row) {
                $fund = $this->createLedgerRecord($data,$previous_row);
            });
        }
        return $fund;
    }

    
    public function getTotalUnLockedCredits(){
        $locked_fund_ids = OrderCommissionLock::where('status',OrderCommissionLock::STATUS_LOCKED)
        ->pluck('wallet_fund_id');
        $total = 0;
        $funds = SuperAdminWalletModel::whereNotIn('id',$locked_fund_ids)
        ->where('ledger_type',WalletModel::LEDGER_CREDIT)->get();
        if(count($funds) > 0){
            foreach($funds as $fund){
                $total += $fund->amount;
            }
        }
        return $total;
    }

    public function getTotalDebits(){
        $funds = SuperAdminWalletModel::where('ledger_type',WalletModel::LEDGER_DEBIT)->get();
        $total = 0;
        if(count($funds) > 0){
            foreach($funds as $fund){
                $total += $fund->amount;
            }
        }
        return $total;
    }

    public function getTotalLockedCredits(){
       $locked_fund_ids = OrderCommissionLock::where('status',OrderCommissionLock::STATUS_LOCKED)->pluck('wallet_fund_id');
       $total = 0;
       if(count($locked_fund_ids) > 0){
            $funds = SuperAdminWalletModel::whereIn('id',$locked_fund_ids)->where('ledger_type',WalletModel::LEDGER_CREDIT)
            ->select('id','amount')->get();
            if(count($funds) > 0){
                foreach($funds as $fund){
                    $total += $fund->amount;
                }
            }
       }
       return $total;
    }


    public function getTotalAccountBalance(){
        $balance = $this->getTotalUnLockedCredits() - $this->getTotalDebits();
        return $balance;
    }

    protected function createOrderCommissionLock( $fund,LockDetails $lock_details){
        return OrderCommissionLock::create([
            'user_id' => $fund->sender_id,
            'store_id' => $lock_details->store_id,
            'order_id' => $lock_details->order_id,
            'sub_order_id' => $lock_details->sub_order_id,
            'wallet_fund_id' => $fund->id,
            'status' => OrderCommissionLock::STATUS_LOCKED
        ]);
    }

    protected function getPreviousRow(){
        return DB::table('super_admin_wallet')->orderBy('id','desc')->first();
    }



    public function depositFund($amount,SenderObject $sender = null, TransactionDetails $trx_details = null){
        $previous_row = $this->getPreviousRow();
        $data = [
            'amount' => $amount,
            'previous_row_hash' => $this->generateHashFromRow($previous_row),
            'ledger_type' => SuperAdminWalletModel::LEDGER_CREDIT
        ];
        if(isset($sender)){
            $data['sender_id'] = $sender->sender_id;
            $data['sender_type'] = $sender->sender_type;
        }
        if(isset($trx_details)){
            $data['transaction_id'] = $trx_details->transaction->id;
            $data['transaction_type'] = $trx_details->transaction_type;
        }
        return $this->createLedgerRecord($data,$previous_row);
    }

    protected function createLedgerRecord($data,$previous_row){
        $fund = null;
        DB::transaction(function()use(&$fund,$previous_row,$data){
            SuperAdminWalletModel::create($data);
            $fund = $this->getPreviousRow();
            //$hashable_row = $this->convertRowAmount($fund);
            if(isset($previous_row)){
                DB::table('super_admin_wallet')
                ->where('id',$previous_row->id)
                ->update([
                    'next_row_hash' => $this->generateHashFromRow($fund)
                ]);
            }
        });
        return $fund;
    }

    public function depositLockedOrderFund( $amount, SenderObject $sender, LockDetails $lock_details, ?TransactionDetails $trx_details = null)
    {
        $previous_row = $this->getPreviousRow();
        //$hashable_row = $this->convertRowAmount($previous_row);
        $data = [
            'amount' => $amount,
            'previous_row_hash' => $this->generateHashFromRow($previous_row),
            'sender_id' => $sender->sender_id,
            'sender_type' => $sender->sender_type,
            'ledger_type' => SuperAdminWalletModel::LEDGER_CREDIT
        ];
        if(isset($trx_details)){
            $data['transaction_id'] = $trx_details->transaction->id;
            $data['transaction_type'] = $trx_details->transaction_type;
        }
        $fund = $this->createLedgerRecord($data,$previous_row); 
        return $this->createOrderCommissionLock($fund,$lock_details);
    }


}
    