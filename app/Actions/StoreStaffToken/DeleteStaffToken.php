<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaffToken;
use App\Traits\HasRoles;
use App\Traits\HasStoreRoles;

class DeleteStaffToken extends Action{
   use HasRoles,HasStoreRoles;
   protected $request;
   protected $id;
   protected $user;

   public function __construct(Request $request,$id){
      $this->request=$request;
      $this->user = $request->user();
      $this->id = $id;
   }

   protected function validate(){
      $val = Validator::make(['store_staff_token_id'=>$this->id],[
         'store_staff_token_id'=>'required|integer|exists:store_staff_tokens,id'
      ]);
      return $this->valResult($val);
   }


   protected function isUserEligible($token){
      if($this->isStoreOwner($this->user->user_type)){
         return true;
      } else if($this->isStoreStaff($this->user->user_type)){
         $role = $this->getStoreRoleId($this->user->id,$token->store_id);
         if(!isset($role)) return false;
         if($token->staff_type >= $role){
            return false;
         } 
         return true;
      }
      
      return false;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         $token = StoreStaffToken::find($this->id);
         if(isset($token)){
            if($this->isUserEligible($token)){
               $token->delete();
            } else {
               return $this->validationError('You are not eligible to delete token(s) of this level.');
            }
         }
         return $this->successMessage('Successfully deleted staff token.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   