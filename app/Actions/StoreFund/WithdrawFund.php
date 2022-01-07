<?php
namespace App\Actions\StoreFund;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Services\WalletServices\StoreWallet;
use App\Traits\HasStore;

class WithdrawFund extends Action{
   use HasStore;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'amount' => 'required|numeric',
         'store_id' => $this->storeIdValidationRule(),
         'bank_account_id' => 'required|integer|store_bank_accounts,id'
      ]);
      return $this->valResult($val);
   }

   

   public function execute(){
      try{
         /*$val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);*/
         $wallet = new StoreWallet($this->request->store_id);
         $data = [
            'locked_amount' => $wallet->getTotalAccountBalance(),
            'locked_credits' => $wallet->getTotalLockedCredits(),
            'unlocked_credits' => $wallet->getTotalUnLockedCredits(),
            'total_debits' => $wallet->getTotalDebits()
         ];
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   