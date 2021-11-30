<?php
namespace App\Actions\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class InitializePayment extends Action{
   protected $request;
   public function __construct(Request $request){
   $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'payment_gateway' => 'required|integer',
      ]);
      return $this->valResult($val);
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   