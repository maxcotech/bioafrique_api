<?php
namespace App\Services\WalletServices;

use App\Models\OrderCommissionLock;
use App\Models\SuperAdminWallet as SuperAdminWalletModel;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\TransactionDetails;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\Wallet;

class SuperAdminWallet implements Wallet{

    public function __construct(){
       //
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
    