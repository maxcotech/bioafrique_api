<?php
namespace App\Actions\ShippingGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShippingGroup;
use App\Traits\HasRateConversion;
use App\Traits\HasStore;
use Illuminate\Validation\Rule;

class CreateShippingGroup extends Action{
   use HasStore,HasRateConversion;

   protected $request;
   protected $max_range_count = 100;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'group_name' => ['required','string',
            Rule::unique('shipping_groups','group_name')->where(function($query){
               return $query->where('store_id',$this->request->store_id);
            })],
         'shipping_rate' => 'required|numeric',
         'high_value_rate' => 'nullable|numeric',
         'mid_value_rate' => 'nullable|numeric',
         'low_value_rate' => 'nullable|numeric',
         'delivery_duration' => 'required|integer',
         'door_delivery_rate' => 'nullable|numeric',
         'dimension_range_rates' => 'nullable|json'
      ]);
      return $this->valResult($val);
   }

   protected function validateDimensionRangeRates($rates){
      if(isset($rates) && is_array($rates) && count($rates) > 0){
         if(count($rates) > $this->max_range_count){
            return $this->payload(
               "failed",[],"The dimension range rates you submitted has entries above the maximum amount allowed, which is ".$this->max_range_count
            );
         }
         foreach($rates as $rate){
            $val = Validator::make($rate,[
               'max' => 'required|numeric|max:99999999',
               'min' => 'required|numeric|max:99999999',
               'rate' => 'required|numeric'
            ]);
            if($val->fails()){
               return $this->valResult($val);
            }

         }
      }
      return $this->payload();
   }

   protected function onCreateShippingGroup($dimension_rates){
      ShippingGroup::create([
         'store_id' => $this->request->store_id,
         'group_name' => $this->request->group_name,
         'shipping_rate' => $this->request->shipping_rate,
         'high_value_rate' => $this->request->input('high_value_rate',0),
         'mid_value_rate' => $this->request->input('mid_value_rate',0),
         'low_value_rate' => $this->request->input('low_value_rate',0),
         'delivery_duration' => $this->request->delivery_duration,
         'door_delivery_rate' => $this->request->input('door_delivery_rate',0),
         'dimension_range_rates' => json_encode($dimension_rates)
      ]);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $dim_range_rates = null;
         if($this->request->input('dimension_range_rates',null) != null){
            $dim_range_rates = json_decode($this->request->input('dimension_range_rates',null),true);
         }
         $val2 = $this->validateDimensionRangeRates($dim_range_rates);
         if($val2['status'] != "success") return $this->resp($val2);
         $this->onCreateShippingGroup($dim_range_rates);
         return $this->successMessage('Shipping Group was successfully created. proceed to add shipping locations.');

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   