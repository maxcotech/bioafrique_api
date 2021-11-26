<?php
namespace App\Actions\ShippingLocation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShippingLocation;
use App\Traits\HasStore;

class DeleteShippingLocation extends Action{
   use HasStore;
   protected $request;
   protected $location_id;
   public function __construct(Request $request,$location_id){
      $this->request=$request;
      $this->location_id = $location_id;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['location_id'] = $this->location_id;
      $val = Validator::make($data,[
         'location_id' => 'required|integer|exists:shipping_locations,id',
         'store_id' => $this->storeIdValidationRule()
      ]);
      return $this->valResult($val);
   }

   protected function deleteLocation(){
      ShippingLocation::where('store_id',$this->request->query('store_id'))
      ->where('id',$this->location_id)
      ->delete();
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $this->deleteLocation();
         return $this->successMessage('Shipping location successfully deleted.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   