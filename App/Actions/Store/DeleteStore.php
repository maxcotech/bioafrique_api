<?php
namespace App\Actions\Store;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Traits\HasRoles;

class DeleteStore extends Action{
   use HasRoles;
   protected $request;
   protected $user;
   protected $store_id;
   public function __construct(Request $request,$store_id){
      $this->request=$request;
      $this->user = $request->user();
      $this->store_id = $store_id;
   }

   protected function userIsEligible(){
      $user_type = $this->user->user_type;
      if($this->isStoreOwner($user_type)){
         if(Store::where('id',$this->store_id)->where('user_id',$this->user->id)->exists()){
            return true;
         }
         return false;
      } elseif($this->isSuperAdmin($user_type)){
         return true;
      } else {
         return false;
      }
   }
  
   public function execute(){
      try{
         if($this->userIsEligible()){
            Store::where('id',$this->store_id)->delete();
            return $this->successMessage('Store account was deleted successfully.');
         } 
         return $this->notAuthorized("You are not authorized to carry out this operation.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   