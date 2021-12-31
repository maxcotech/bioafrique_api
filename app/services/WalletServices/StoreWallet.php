<?php
namespace App\Services\WalletServices;

use App\Models\OrderFundLock;
use App\Models\StoreWallet as StoreWalletModel;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\TransactionDetails;
use App\Traits\HasEncryption;
use App\Traits\TokenGenerator;
use Illuminate\Support\Facades\DB;

class StoreWallet extends WalletService {
    use HasEncryption,TokenGenerator;
    protected $store_id;
    public function __construct($store_id){
       parent::__construct();
       $this->store_id = $store_id;
    }

    public function getTotalUnLockedCredits(){

    }

    public function getTotalDebits(){
        
    }

    

    public function getTotalLockedCredits(){
       //
    }

    public function getTotalPendingWithdrawal(){
       //
    }

    public function getTotalAccountBalance(){
        /*$balance = $this->getTotalUnLockedCredits() - $this->getTotalDebits();
        return $balance;*/
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

    protected function getPreviousRow(){
        return StoreWalletModel::where('store_id',$this->store_id)
        ->orderBy('id','desc')->first();
    }

    protected function createLedgerRecord($data,$previous_row){
        $fund = null;
        DB::transaction(function()use($data,&$fund,$previous_row){
            $fund = StoreWalletModel::create($data);
            $hashable_fund = $this->convertRowAmount($fund);
            if(isset($previous_row)){
                DB::table('store_wallets')
                ->where('id',$previous_row->id)
                ->update([
                    'next_row_hash' => $this->generateHashFromRow($hashable_fund)
                ]);

            }
        });
        return $fund;
    }

    public function depositLockedOrderFund(
        $amount, SenderObject $sender, 
        LockDetails $lock_details,TransactionDetails $trx_details = null){
        $previous_row = $this->getPreviousRow();
        $hashable_row = $this->convertRowAmount($previous_row);
        $data = [
            'store_id' => $this->store_id,
            'previous_row_hash' => $this->generateHashFromRow($hashable_row),
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

}
    