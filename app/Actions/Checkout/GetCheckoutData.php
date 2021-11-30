<?php
namespace App\Actions\Checkout;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Traits\HasShipping;

class GetCheckoutData extends Action{
   use HasShipping;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function getCheckoutDetails(){
      $data = [];
      $data['shipping_details'] = $this->collateShippingDetailsByLocation($this->user,true);
      $data['current_billing_address'] = $this->getCurrentBillingAddress($this->user->id);
      return $data;
   }


   public function execute(){
      try{
         $data = $this->getCheckoutDetails();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   