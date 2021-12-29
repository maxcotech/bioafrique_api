<?php
namespace App\Actions\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\SuperAdminWallet;
use App\Models\WalletModel;
use App\Services\WalletServices\SuperAdminWallet as SuperAdminWalletService;

class DebitWallet extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'amount' => 'required|numeric',
         'recipient' => 'nullable|integer',
         'recipient_id' => $this->getRecipientIdRule()
      ]);
      return $this->valResult($val);
   }

   protected function getRecipientIdRule(){
      $recipient = $this->request->input('recipient',null);
      if($recipient != null){
         if($recipient == WalletModel::STORE_WALLET){
            return 'required|integer|exists:store_wallets,id';
         } elseif ($recipient == WalletModel::USER_WALLET){
            //TODO: To be implemented
            throw new \Exception('User wallets are not supported yet');
         } elseif ($recipient == WalletModel::SUPER_ADMIN_WALLET) {
            throw new \Exception('You can not send transactions to sender wallet.');
         } else {
            throw new \Exception('The submitted recipient is not supported');
         }
      } else {
         return "nullable";
      }
   }

   protected function onDebitAccount(){
      
   }

   public function execute(){
      try{
         //$val = $this->validate();
         //if($val['status'] !== "success") return $this->resp($val);
         return $this->successWithData((new SuperAdminWalletService())->historyIsValid());
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   