<?php
namespace App\Actions\WithdrawalRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Exceptions\InvalidTransactionHistory;
use App\Services\WalletServices\StoreWallet;
use App\Traits\HasStore;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CreateRequest extends Action{
   use HasStore;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'amount' => 'required|numeric',
         'store_id' => ['required','integer',Rule::exists('stores','id')->where(function($query){
            $query->where('user_id',$this->user->id);
         })],
         'bank_account_id' => ['required','integer',Rule::exists('store_bank_accounts','id')->where(function($query){
            $query->where('store_id',$this->request->store_id);
         })],
         'password' => 'required|string'
      ]);
      return $this->valResult($val);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if(!Hash::check($this->request->password,$this->user->password)){
            return $this->validationError("The password you entered is incorrect.");
         }
         $wallet = new StoreWallet($this->request->store_id);
         $amount = $this->request->amount;
         $bank_id = $this->request->bank_account_id;
         $wallet->createWithdrawalRequest($amount,$bank_id);
         return $this->successMessage('Withdrawal request posted successfully.');
      }
      catch(\App\Exceptions\InsufficientFund $e){
         return $this->validationError($e->getMessage());
      }
      catch(InvalidTransactionHistory $e){
         return $this->internalError($e->getMessage());
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   