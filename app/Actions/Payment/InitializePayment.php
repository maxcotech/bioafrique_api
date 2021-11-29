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
   public function execute(){
      try{
         //
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   