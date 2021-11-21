<?php
namespace App\Actions\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Models\State;
use App\Models\Store;
use App\Traits\StringFormatter;
use Illuminate\Validation\Rule;

class UpdateStore extends Action{
   use StringFormatter;

   protected $request;
   protected $user;

   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $this->request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => 'required|integer|exists:stores,id',
         'store_name' => [
            'required','string',Rule::unique('stores','store_name')
               ->where(function($query){
               return $query->where('id','!=',$this->request->store_id);
            })],
         
         'country_id' => 'required|integer|exists:countries,id',
         'store_address' => 'required|string',
         'store_email' => 'nullable|email',
         'store_telephone' => 'nullable|numeric',
         'state' => ['required','string',Rule::exists('states','state_name')->where(function($query){
            return $query->where('country_id',$this->request->country_id);
         })],
         'city' => ['required','string',Rule::exists('cities','city_name')->where(function($query){
            $state = State::where('country_id',$this->request->country_id)
            ->where('state_name',$this->request->state)->first();
            if(isset($state)){
               return $query->where('state_id',$state->id);
            }
         })]
      ]);
      return $this->valResult($val);
   }

   protected function generateNewStoreSlug(){
      $store_name = $this->request->store_name;
      $store_id = $this->request->store_id;
      $slug = $this->generateSlugFromString($store_name);
      if(Store::where('id','!=',$store_id)->where('store_slug',$slug)->exists()){
         throw new \Exception("The category title you are trying to use, already belongs to another category.");
      }
      return $slug;
   }

   protected function updateStore($slug,$state,$city){
      Store::where('user_id',$this->user->id)
      ->where('id',$this->request->store_id)->update([
         'store_name' => $this->request->store_name,
         'store_slug' => $slug,
         'country_id' => $this->request->country_id,
         'store_address' => $this->request->store_address,
         'store_email' => $this->request->store_email,
         'store_telephone' => $this->request->store_telephone,
         'state_id' => $state->id,
         'city_id' => $city->id
      ]);
   }

   protected function getCityFromNameAndState($state){
      $city = City::where('state_id',$state->id)
      ->where('city_name',$this->request->city)
      ->first();
      return $city;
   }

   protected function getStateFromNameAndCountry(){
      $state = State::where('state_name',$this->request->state)
      ->where('country_id',$this->request->country_id)->first();
      return $state;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $slug = $this->generateNewStoreSlug();
         $state = $this->getStateFromNameAndCountry();
         $city = $this->getCityFromNameAndState($state);
         $this->updateStore($slug,$state,$city);
         return $this->successMessage('Your store update was uploaded successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   