<?php
namespace App\Actions\ShippingLocation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShippingLocation;
use App\Traits\HasStore;
use Illuminate\Validation\Rule;

class GetShippingLocations extends Action{
   use HasStore;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'shipping_group_id' => ['required','integer',Rule::exists('shipping_groups','id')
         ->where(function($query){
            return $query->where('store_id',$this->request->store_id);
         })],
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getShippingLocations(){
      return ShippingLocation::with(['country:id,country_name','state:id,state_name','city:id,city_name'])
      ->where('shipping_group_id',$this->request->query('shipping_group_id'))
      ->where('store_id',$this->request->query('store_id'))
      ->paginate(
         $this->request->query('limit',3),
         ['shipping_group_id','id','store_id','state_id','city_id','country_id']);
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $locations = $this->getShippingLocations();
         return $this->successWithData($locations);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   