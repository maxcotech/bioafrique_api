<?php
namespace App\Actions\ShippingGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShippingGroup;
use App\Traits\HasStore;

class GetShippingGroups extends Action{
   use HasStore;

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getShippingGroups(){
      return ShippingGroup::where(
         'store_id',$this->request->query('store_id')
      )->paginate(
         $this->request->query('limit',15),
         [
            'id','store_id','group_name','shipping_rate',
            'high_value_rate','mid_value_rate','low_value_rate',
            'dimension_range_rates','delivery_duration','door_delivery_rate'
         ]
      );
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $groups = $this->getShippingGroups();
         return $this->successWithData($groups);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   