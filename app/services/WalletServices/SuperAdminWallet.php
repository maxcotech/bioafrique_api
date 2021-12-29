<?php
namespace App\Services\WalletServices;

use App\Models\OrderCommissionLock;
use App\Models\SuperAdminWallet as SuperAdminWalletModel;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\TransactionDetails;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\Wallet;
use App\Models\WalletModel;

class SuperAdminWallet implements Wallet{

    public function __construct(){
       //
    }

    public function historyIsValid(){
        $transactions = SuperAdminWalletModel::all();
        $trx_count = count($transactions);
        $is_valid = true;
        if($trx_count > 0){
            for($i = 0; $i < $trx_count; $i++){
                if($i > 0){
                    $curr_trx = $transactions[$i];
                    $previous_trx = $transactions[$i - 1];
                    $previous_hash = $curr_trx->previous_row_hash;
                    if(!Hash::check($previous_trx,$previous_hash)){
                        $is_valid = false;
                    }
                }
            }
        }
        return $is_valid;
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

    public function getTotalPendingWithdrawal(){
       //
    }

    public function getTotalAccountBalance(){
        $balance = $this->getTotalUnLockedCredits() - $this->getTotalDebits();
        return $balance;
    }

    protected function getPreviousRowHash(){
        $row = SuperAdminWalletModel::orderBy('id','desc')->first();
        if(isset($row)){
            return Hash::make($row);
        }
        return null;
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

    public function depositLockedOrderFund( $amount, SenderObject $sender, LockDetails $lock_details, ?TransactionDetails $trx_details = null)
    {
        $data = [
            'amount' => $amount,
            'previous_row_hash' => $this->getPreviousRowHash(),
            'sender_id' => $sender->sender_id,
            'sender_type' => $sender->sender_type,
            'ledger_type' => SuperAdminWalletModel::LEDGER_CREDIT
        ];
        if(isset($trx_details)){
            $data['transaction_id'] = $trx_details->transaction->id;
            $data['transaction_type'] = $trx_details->transaction_type;
        }
        $fund = SuperAdminWalletModel::create($data);
        return $this->createOrderCommissionLock($fund,$lock_details);
    }


}
    