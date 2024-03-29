<?php
namespace App\Actions\User;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\StoreResource;
use App\Http\Resources\UserResource;
use App\Traits\HasCookie;
use App\Traits\HasRateConversion;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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


   protected function appendStoreData($data,$user){
      if(isset($user)){
         Log::alert("appending user store data to payload");
         if($this->isStoreOwner()){
            $store = $user->store;
            $data['current_store'] = (isset($store) && !empty($store))? new StoreResource($store): null;
            $data['stores'] = []; 
         } elseif($this->isStoreStaff()) {
            $stores = $user->workStores;
            $data['stores'] = isset($stores)? StoreResource::collection($stores) : [];
            $data['current_store'] = null;
         } else {
            Log::alert('could not append user store data, because user doesnt seem to be a store staff');
         }
      } else {
         Log::alert('Could not append user store data, because user instance is null');
      }
      return $data;
   }

   
   public function execute(){
      try{
         $data = [];
         $cookie = $this->getUserByCookie();
         $user = Auth::user();
         $currency = $this->getUserCurrency($user,$cookie);
         $country = $this->getUserCountry($user,$cookie);
         $data['currency'] = isset($currency)? new CurrencyResource($currency):null;
         $data['country'] = isset($country)? new CountryResource($country):null;
         $data['user'] = isset($user)? new UserResource($user):null;
         $data['logged_in'] = isset($user)? true:false;
         $data = $this->appendStoreData($data,$user);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   