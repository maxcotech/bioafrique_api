<?php
namespace App\Actions\StoreBankAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Models\StoreBankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateAccount extends Action{
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => ['required','integer',Rule::exists('stores','id')->where(function($query){
            $query->where('user_id',$this->user->id);
         })],
         'id' => ['required','integer',Rule::exists('store_bank_accounts','id')->where(function($query){
            $query->where('store_id',$this->request->store_id);
         })],
         'bank_name' => 'required|string',
         'bank_code' => 'nullable|string',
         'account_number' => 'required|numeric',
         'bank_currency_id' => 'required|integer|exists:currencies,id',
         'password' => 'required|string'
      ]);
      return $this->valResult($val);
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $store = Store::find($this->request->store_id);
         $user = User::find($store->user_id);
         if(Hash::check($this->request->password,$user->password)){
            StoreBankAccount::where('id',$this->request->id)->update($this->request->all([
               'bank_name','bank_code','account_number','bank_currency_id','store_id'
            ]));
            return $this->successMessage('Bank Account was updated successfully.');
         } else {
            return $this->validationError('The password you entered is incorrect.');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   