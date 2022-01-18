<?php 
namespace App\Services\WalletServices;

use App\Interfaces\Wallet;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\TransactionDetails;
use App\Services\WalletServices\Utilities\TransferRecipient;
use App\Traits\HasArrayOperations;
use App\Traits\HasRateConversion;
use Illuminate\Support\Facades\Log;

abstract class WalletService implements Wallet{
    use HasArrayOperations,HasRateConversion;
    protected $user;
    protected $hashable_fields;
    public function __construct()
    {
        $this->user = request()->user();
        $this->hashable_fields = [
         'amount','previous_row_hash','sender_id',
         'sender_type','ledger_type','transaction_type','transaction_id',
         'created_at'
     ];

    }

    protected function convertRowAmount($row){
        $output = [];
        if(!isset($row)) return null;
        $input = json_decode(json_encode($row),true);
        foreach($input as $key => $value){
            if($key == "amount"){
                $output[$key] = $this->userToBaseCurrency($value,$this->user);
            } else {
                $output[$key] = $value;
            }
        }
        $output_obj = json_decode(json_encode($output));
        return $output_obj;
    }

    protected function generateHashFromRow($row){
        $selected = $this->hashable_fields;
        if(isset($row)){
            $hash_array = $this->serializeObject($row,$selected,"NA");
            return $this->hashArrayItems($hash_array);
        }
        return null;
    }


    public function historyIsValid($transactions){
        $selected = $this->hashable_fields;
        $trx_count = count($transactions);
        $is_valid = true;
        if($trx_count > 0){
            for($i = 0; $i < $trx_count; $i++){
                if($i > 0){
                    $curr_row = $transactions[$i];
                    $previous_row = $transactions[$i - 1];
                    $previous_hash = $curr_row->previous_row_hash;
                    $current_hash = $previous_row->next_row_hash;
                    $curr_row_array = $this->serializeObject($curr_row,$selected,"NA");
                    $previous_row_array = $this->serializeObject($previous_row,$selected,"NA");
                    if(!$this->checkArrayHash($previous_row_array,$previous_hash)){
                        Log::alert('previous row is not valid '.json_encode($previous_row_array));
                        $is_valid = false; 
                    }
                    if(!$this->checkArrayHash($curr_row_array,$current_hash)){
                        Log::alert('current row is not valid '.json_encode($curr_row_array));
                        $is_valid = false;

                    }
                }
            }
        }
        return $is_valid;
    }

    public function depositLockedOrderFund($amount, SenderObject $sender, LockDetails $lock_details, ?TransactionDetails $trx_details = null)
    {
        
    }

    public function getTotalDebits()
    {
        
    }

    public function getTotalLockedCredits()
    {
        
    }

    public function getTotalUnLockedCredits()
    {
        
    }

    public function getTotalPendingWithdrawal()
    {
        
    }

    public function getTotalAccountBalance()
    {
        
    }

    public function withdrawFund($amount)
    {
        
    }

    public function transferFund($amount, TransferRecipient $recipient)
    {
        
    }

    


}