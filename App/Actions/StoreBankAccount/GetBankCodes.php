<?php
namespace App\Actions\StoreBankAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Country;
use App\Models\Currency;
use App\Services\BankServices;

class GetBankCodes extends Action{
   protected $request;
   protected $currency_id;
   public function __construct(Request $request,$currency_id){
      $this->request=$request;
      $this->currency_id = $currency_id;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['currency_id'] = $this->currency_id;
      $val = Validator::make($data,[
         'currency_id' => 'required|integer|exists:currencies,id'
      ]);
      return $this->valResult($val);
   }

   protected function onGetBankCodes(){
      $bankService = new BankServices();
      $currency = Currency::find($this->currency_id);
      $country = Country::find($currency->country_id ?? null);
      if(!isset($country)) throw new \Exception('The currency you selected is not supported.');
      $result = $bankService->getBankCodes($country->country_code);
      if($result->status != "success"){
         throw new \Exception($result->message);
      }
      return $result->data;
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $codes = $this->onGetBankCodes();
         return $this->successWithData($codes);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   