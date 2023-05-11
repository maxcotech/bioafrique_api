<?php
namespace App\Actions\ShippingLocation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShippingLocation;
use App\Traits\HasShipping;
use App\Traits\HasStore;
use Illuminate\Validation\Rule;

class CreateShippingLocation extends Action{
   use HasStore,HasShipping;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'shipping_group_id' => ['required','integer',
            Rule::exists('shipping_groups','id')->where(function($query){
               return $query->where('store_id',$this->request->store_id);
            })],
         'country_id' => 'required|integer|exists:countries,id',
         'state' => ['required','string',Rule::exists('states','state_name')->where(function($query){
            return $query->where('country_id',$this->request->country_id);
         })],
         'city' => ['nullable','string',Rule::exists('cities','city_name')->where(function($query){
            $state = $this->getStateModelByName();
            if(isset($state)){
               return $query->where('state_id',$state->id);
            } 
            return $query;
         })]
      ]);
      return $this->valResult($val);
   }

   

   protected function configurationExists($state_id,$city_id){
      return ShippingLocation::where('country_id',$this->request->country_id)
      ->where('state_id',$state_id)
      ->where('city_id',$city_id)
      ->where('store_id',$this->request->store_id)
      ->exists();
   }

   protected function createLocationConfig($state_id,$city_id){
      ShippingLocation::create([
         'country_id' => $this->request->country_id,
         'state_id' => $state_id,
         'city_id' => $city_id,
         'store_id' => $this->request->store_id,
         'shipping_group_id' => $this->request->shipping_group_id
      ]);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $state = $this->getStateModelByName();
         $state_id = isset($state)? $state->id : null;
         $city = $this->getCityModelByName($state_id);
         $city_id = isset($city)? $city->id : null;
         if($this->configurationExists($state_id,$city_id)){
            return $this->validationError('The location configuration you submitted already exists in your store instance.');
         }
         $this->createLocationConfig($state_id,$city_id);
         return $this->successMessage("Shipping location configuration created successfully.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   