<?php
namespace App\Actions\StoreBankAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreBankAccount;
use App\Traits\HasRoles;
use App\Traits\HasStore;

class DeleteAccount extends Action{
   use HasStore,HasRoles;
   protected $request;
   protected $account_id;
   protected $user;
   public function __construct(Request $request,$account_id){
      $this->request=$request;
      $this->account_id = $account_id;
      $this->user = $request->user();
   }
   
   protected function validate(){
      $data = $this->request->all();
      $data['account_id'] = $this->account_id;
      $val = Validator::make($data,[
         'account_id' => 'required|integer|exists:store_bank_accounts,id',
         'store_id' => $this->getStoreIdRules()
      ]);
      return $this->valResult($val);
   }

   protected function getStoreIdRules(){
      $user_type = $this->user->user_type;
      if($this->isSuperAdmin($user_type)){
         return 'nullable|integer|exists:stores,id';
      } elseif($this->isStoreOwner($user_type)){
         return $this->storeIdValidationRule();
      } else {
         throw new \Exception('You are not authorized to carry out this operation.');
      }
   }

   protected function onDelete(){
      $store_id = $this->request->query('store_id',null);
      $query = StoreBankAccount::where('id',$this->account_id);
      if(isset($store_id)){
         $query = $query->where('store_id',$store_id);
      }
      $query->delete();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $this->onDelete();
         return $this->successMessage('Bank account successfully deleted');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   