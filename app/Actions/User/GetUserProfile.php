<?php
namespace App\Actions\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Cookie;
use Illuminate\Support\Facades\Auth;

class GetUserProfile extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function getUserCountry($user,$cookie){
      if(isset($user)){
         return $user->country()->first();
      } else if(isset($cookie)) {
         return $cookie->country()->first();
      }
      return null;
   }

   protected function getUserCurrency($user,$cookie){
      if(isset($user)){
         return $user->currency()->first();
      } else if(isset($cookie)) {
         return $cookie->currency()->first();
      }
      return null;
   }

   
   public function execute(){
     // try{
         $data = [];
         $cookie = Cookie::where('cookie_value',$this->request->cookie('basic_access'))->first();
         $user = Auth::user();
         $data['currency'] = $this->getUserCurrency($user,$cookie);
         $data['country'] = $this->getUserCountry($user,$cookie);
         $data['user'] = $user;
         $data['logged_in'] = isset($user)? true:false;
         return $this->successWithData($data);
     /* }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }*/
   }

}
   