<?php
namespace App\Actions\StoreStaffToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaffToken;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use Illuminate\Support\Facades\DB;

class CreateStoreStaffToken extends Action{
   use HasStore,HasRoles;
   protected $request,$user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'staff_type' => ['required','integer'],
         'amount' => ['required','integer','max:100,min:1']
      ]);
      return $this->valResult($val);
   }

   protected function getNewToken(){
      $token = null;
      while(true){
         $token = $this->generateStoreStaffToken($this->request->store_id);
         if(isset($token)){
            if(StoreStaffToken::where('staff_token',$token)->exists() == false){
               break;
            }
         } else {
            break;
         }
      }
      return $token;
      
   }

   protected function saveStoreStaffTokens(){
      DB::transaction(function(){
         $amount = $this->request->amount;
         $staff_type = $this->request->staff_type;
         $store_id = $this->request->store_id;

         for($i = 0;$i < $amount;$i++){
            $token = $this->getNewToken();
            StoreStaffToken::create([
               'staff_token' => $token,
               'staff_type' => $staff_type,
               'store_id' => $store_id
            ]);
         }
      });
      
   }

   protected function staffTypeIsValid(){
      $staff_type = $this->request->staff_type;
      if($this->isStoreManager($staff_type) || $this->isStoreWorker($staff_type)){
         return true;
      } else {
         return false;
      }
   }




   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->staffTypeIsValid()){
            $this->saveStoreStaffTokens();
            return $this->successMessage('Successfully created staff token(s)');
         } else {
            return $this->validationError('The Staff Type you selected is invalid.');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   