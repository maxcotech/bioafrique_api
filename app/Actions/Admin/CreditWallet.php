<?php
namespace App\Actions\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Services\WalletServices\SuperAdminWallet;

class CreditWallet extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'amount' => 'required|numeric'
      ]);
      return $this->valResult($val);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $wallet = new SuperAdminWallet();
         return $this->successWithData($wallet->depositFund($this->request->amount),'Your wallet was successfully credited.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   