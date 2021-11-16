<?php
namespace App\Actions\BillingAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class CreateBillingAddress extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'country' => 'required|string|exists:countries,country_name',
         'state' => 'required|string|exists:states,state_name',
         'city' => 'required|string|exists:cities,city_name',
         'street_address' => 'required|string',
         'postal_code' => 'nullable|string',
         'telephone_number' => 'required|integer',
         'telephone_code' => 'required|string|exists:countries,country_tel_code',
         'additional_number' => 'nullable|integer',
         'additional_tel_code' => 'exclude_if:addtional_number,null|required|string'
      ]);
      return $this->valResult($val);
   }

   protected function removeCurrentStatusFromAddresses(){
      
   }




   public function execute(){
      try{
         //
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   