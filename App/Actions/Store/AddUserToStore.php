<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaff;
use App\Models\StoreStaffToken;
use App\Traits\HasRoles;
use App\Traits\HasUserStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AddUserToStore extends Action{
   use HasRoles,HasUserStatus;
   protected $request,$user;

   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => 'required|integer|exists:stores,id',
         'access_key' => ['required','string',Rule::exists('store_staff_tokens','staff_token')
            ->where(function($query){
               $query->where('expired',0);
            })
         ]
      ]);
      return $this->valResult($val);
   }

   protected function userIsEligible(){
      if($this->isStoreStaff()){
         return true;
      }
      return false;
   }

   protected function userAlreadyExistsInStore(){
      return StoreStaff::where('user_id',$this->user->id)
      ->where('store_id',$this->request->store_id)
      ->exists();
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->userIsEligible()){
            $token = StoreStaffToken::where('staff_token',$this->request->access_key)
            ->where('store_id',$this->request->store_id)
            ->where('expired',0)->first();
            if(isset($token)){
               if($this->userAlreadyExistsInStore()){
                  return $this->validationError('You have already joined this store.');
               } else {
                  DB::transaction(function()use($token){
                     StoreStaff::create([
                        'store_id' => $this->request->store_id,
                        'user_id' => $this->user->id,
                        'staff_type' => $token->staff_type,
                        'status' => $this->getActiveUserId()
                     ]);
                     $token->update([
                        'expired'=> 1
                     ]);
                  });
                  return $this->successMessage('User account was successfully added to store.');
               }
            } else {
               return $this->validationError('The token you entered is invalid.');
            }
         }
         return $this->notAuthorized("You are not authorized to carry out this operation.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   