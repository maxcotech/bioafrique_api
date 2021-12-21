<?php
namespace App\Actions\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResetPassword extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'old_password' => 'required|string',
         'new_password' => 'required|string|min:8',
         'confirm_password' => 'required|same:new_password'
      ]);
      return $this->valResult($val);
   }

   protected function updateUserPassword(){
      User::where('id',$this->user->id)->update([
         'password' => Hash::make($this->request->new_password)
      ]);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         Log::alert($this->user->password);
         if(Hash::check($this->request->old_password,$this->user->password)){
            $this->updateUserPassword();
            return $this->successMessage('Your password was updated successfully.');
         } else {
            return $this->validationError('The password you entered is incorrect');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   