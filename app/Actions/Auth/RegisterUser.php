<?php
namespace App\Actions\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaff;
use App\Models\StoreStaffToken;
use App\Models\User;
use App\Traits\HasRoles;
use App\Traits\HasUserStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterUser extends Action{
   use HasUserStatus,HasRoles;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }
   protected function validate(){
      $val = Validator::make($this->request->all(),[
      'first_name' => 'required|string',
      'last_name' => 'required|string',
      'phone_number' => 'nullable|integer|min:10',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|string',
      'confirm_password' => 'required|same:password',
      'account_type' => 'required|max:12'
      ]);
      $val->sometimes('telephone_code','required|string',function(){
      if($this->request->filled('phone_number')){
         return true;
      } else {
         return false;
      }
   });
   $val->sometimes('staff_token',$this->staffTokenValidationRules(),function($input){
      if($this->request->account_type != null && $this->request->account_type == $this->getStoreStaffRoleId()){
         return true;
      } 
      return false;
   });
      return $this->valResult($val);
   }

   protected function staffTokenValidationRules(){
      return [
      'required','string',Rule::exists('store_staff_tokens','staff_token')
      ->where(function($query){
         $query->where('expired',0);
      })
      ];
   }
   protected function createUser($user_type = null){
      return User::create([
      'first_name' => $this->request->first_name,
      'last_name' => $this->request->last_name,
      'phone_number' => $this->request->phone_number,
      'email' => $this->request->email,
      'password' => Hash::make($this->request->password),
      'auth_type' => 0,
      'telephone_code'=>$this->request->telephone_code,
      'account_status'=> $this->getActiveUserId(),
      'user_type' => $user_type ?? $this->request->account_type
      ]);
   }

   protected function validateStaffToken($tokenModel){
      if(!isset($tokenModel)) return false;
      if($tokenModel->staff_type != $this->request->account_type){
         return false;
      }
      return true;
   }

   protected function addStaffToStore($user_id,$tokenModel){
      StoreStaff::create([
         'store_id' => $tokenModel->store_id,
         'user_id' => $user_id,
         'staff_type' => $tokenModel->staff_type,
         'status' => $this->getActiveUserId()
      ]);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->request->staff_token !== null){
            $token_model = StoreStaffToken::where('staff_token',$this->request->staff_token)->first();
            if(!$this->validateStaffToken($token_model)) return $this->validationError('The store staff token you entered does not match the type of account you wish to create.');
            if(isset($token_model)){
               DB::transaction(function()use($token_model){
                  $user = $this->createUser($this->getStoreStaffRoleId());
                  $this->addStaffToStore($user->id,$token_model);
                  $token_model->update(['expired' => 1]); //expires token after use
               });
            } else {throw new \Exception("An error occurred, staff token not found.");}
         } else {
            $this->createUser();
         }
         return $this->successMessage('Your account was successfully created.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   