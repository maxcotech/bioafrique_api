<?php
namespace App\Actions\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Currency;
use App\Models\OrderTransaction;
use App\Services\PaymentService;
use App\Traits\HasPayment;
use App\Traits\HasRateConversion;
use Illuminate\Validation\Rule;

class VerifyPayment extends Action{
   use HasPayment,HasRateConversion;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $data = $this->request->all();
      $val = Validator::make($data,[
         'gateway_code' => 'required|integer',
         'gateway_reference' => 'nullable|string',
         'transaction_id' => 'required_if:gateway_code,'.OrderTransaction::FLUTTERWAVE,
         'site_reference' => ['required','string',Rule::exists('order_transactions','reference')->where(function($query){
            return $query->where('user_id',$this->user->id);
         })]
      ]);
      return $this->valResult($val);
   }

   protected function getCurrentTransaction(){
      return OrderTransaction::where('reference',$this->request->site_reference)
      ->where('user_id',$this->user->id)
      ->first();
   }

   protected function markTransactionAsCompleted($transaction){
      $transaction->update([
         'gateway_reference' => $this->request->gateway_reference,
         'status' => OrderTransaction::STATUS_COMPLETED,
      ]);
   }

   protected function markTransactionAsVerified($transaction){
      //to be implemented
   }

   protected function initializeOrder(){

   }

   protected function verifyPayment($transaction){
      $currency = Currency::find($transaction->currency_id);
      $currency_code = $currency->currency_code ?? null;
      $transaction_id = $this->request->transaction_id;
      $amount = $this->convertBaseAmountByRate($transaction->amount,$currency->base_rate);
      $payment_service = new PaymentService(
         $this->request->gateway_code, $transaction->reference,
         $currency_code, $amount
      );
      return $payment_service->verifyPayment($transaction_id);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if(!$this->paymentGatewayExists($this->request->gateway_code)) return $this->validationError('Invalid payment gateway code submitted.');
         $transaction = $this->getCurrentTransaction();
         $this->markTransactionAsCompleted($transaction);
         if($this->verifyPayment($transaction)){

         }
         return $this->internalError('Sorry, your payment verification failed.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   