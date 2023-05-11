<?php
namespace App\Actions\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\UserCurrency;
use App\Traits\HasAuthStatus;
use App\Traits\Message;

class UpdateCurrency extends Action{
   use HasAuthStatus,Message;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'currency_id'=>'required|integer|exists:currencies,id'
      ]);
      return $this->valResult($val);
   }

   protected function updateOrCreate($auth_type){
      UserCurrency::updateOrCreate([
         'user_currencies_type' => $auth_type->type,'user_currencies_id' => $auth_type->id],[
            'currency_id' => $this->request->currency_id
         ]
      );
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $auth_type = $this->getUserAuthTypeObject();
         if(!isset($auth_type)) return $this->validationError($this->getCookieErrorMessage());
         $this->updateOrCreate($auth_type);
         return $this->successMessage('Your currency type was successfully updated');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   