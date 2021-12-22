<?php
namespace App\Actions\User;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUser extends Action{
   protected $request;
   protected $user_id;
   public function __construct(Request $request,$user_id){
      $this->request=$request;
      $this->user_id = $user_id;
   }

   protected function deleteUser(){
      $user = User::find($this->user_id);
      if(isset($user)){
         DB::transaction(function()use($user){
            $user->billingAddresses()->delete();
            $user->storeStaffAccounts()->delete();
            $user->delete();
         });
      }
   }


   public function execute(){
      try{
         $this->deleteUser();
         return $this->successMessage('User Account deleted successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   