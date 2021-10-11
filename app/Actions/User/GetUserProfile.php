<?php
namespace App\Actions\User;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Traits\HasCookie;
use App\Traits\HasRateConversion;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;

class GetUserProfile extends Action{
   use HasRoles,HasCookie,HasRateConversion;

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

   /*protected function getUserCurrency($user,$cookie){
      if(isset($user)){
         return $user->currency()->first();
      } else if(isset($cookie)) {
         return $cookie->currency()->first();
      }
      return null;
   }*/

   protected function appendStoreData($data,$user){
      if(isset($user)){
         if($this->isStoreOwner()){
            $store = $user->store;
            $data['current_store'] = (isset($store) && !empty($store))? $store: null;
            $data['stores'] = (isset($store) && !empty($store))? [$store] : [];
         } elseif($this->isStoreManager() || $this->isStoreWorker()) {
            $stores = $user->workStores;
            $data['stores'] = isset($stores)? $stores : [];
            $data['current_store'] = (isset($stores) && count($stores) > 0)? $stores[0] : null;
         }
      }
      return $data;
   }

   
   public function execute(){
      try{
         $data = [];
         $cookie = $this->getUserByCookie();
         $user = Auth::user();
         $data['currency'] = $this->getUserCurrency($user,$cookie);
         $data['country'] = $this->getUserCountry($user,$cookie);
         $data['user'] = $user;
         $data['logged_in'] = isset($user)? true:false;
         $data = $this->appendStoreData($data,$user);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   