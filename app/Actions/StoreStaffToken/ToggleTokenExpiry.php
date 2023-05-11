<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaffToken;

class ToggleTokenExpiry extends Action{
   protected $request;
   protected $tokenId;
   protected $user;
   public function __construct(Request $request,$id){
      $this->request=$request;
      $this->user = $request->user();
      $this->tokenId = $id;
   }

   protected function validate(){
      $val = Validator::make(['token_id' => $this->tokenId],[
         'token_id' => 'required|integer|exists:store_staff_tokens,id'
      ]);
      return $this->valResult($val);
   }

   protected function getInverseValue($value){
      if($value == 0){
         return 1;
      }
      return 0;
   }

   protected function isUserEligible($staff_type){
      if($staff_type >= $this->user->user_type){
         return false;
      } 
      return true;
   }



   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $token_model = StoreStaffToken::find($this->tokenId);
         if(!$this->isUserEligible($token_model->staff_type)){
            return $this->validationError('You are not eligible to change the status of tokens in this level.');
         }
         $new_value = $this->getInverseValue($token_model->expired);
         if(isset($token_model)){
            $token_model->update([
               'expired' => $new_value
            ]);
         }
         return $this->successMessage($new_value == 1 ? "Token expired." : "Token re-activated" );
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   