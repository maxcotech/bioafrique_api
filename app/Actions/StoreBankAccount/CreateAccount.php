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

class CreateAccount extends Action{
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'bank_name' => 'required|string',
         'account_name' => 'nullable|string',
         'bank_code' => 'nullable|string',
         'account_number' => 'required|numeric',
         'store_id' => ['required','integer',Rule::exists('stores','id')
         ->where(function($query){
            $query->where('user_id',$this->user->id);
         })],
         'bank_currency_id' => 'required|integer|exists:currencies,id',
         'password' => 'required|string'
      ]);
      return $this->valResult($val);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $store = Store::find($this->request->store_id);
         $user = User::find($store->user_id);
         if(Hash::check($this->request->password,$user->password)){
            StoreBankAccount::create($this->request->all(
               ['bank_name','bank_code','account_number','bank_currency_id','store_id','account_name']
            ));
            return $this->successMessage("Bank Account was successfully added");
         } else {
            return $this->validationError("The password you entered is incorrect");
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   