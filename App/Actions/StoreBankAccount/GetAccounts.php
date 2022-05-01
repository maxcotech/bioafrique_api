<?php
namespace App\Actions\StoreBankAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreBankAccount;
use App\Traits\HasRoles;
use App\Traits\HasStore;

class GetAccounts extends Action{
   use HasRoles,HasStore;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user=$request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->getStoreIdRules(),
         'currency_id' => 'nullable|integer|exists:currencies,id',
         'limit' => 'nullable|integer',
         'paginate' => 'nullable|integer|min:0,max:1'
      ]);
      return $this->valResult($val);
   }

   protected function getStoreIdRules(){
      $user_type = $this->user->user_type;
      if($this->isSuperAdmin($user_type)){
         return "nullable|integer|exists:stores,id";
      } elseif($this->isStoreOwner($user_type) || $this->isStoreStaff($user_type)) {
         return $this->storeIdValidationRule();
      } else {
         throw new \Exception('You are not authorized to make this request');
      }
   }

   protected function onGetAccounts(){
      $store_id = $this->request->query('store_id',null);
      $currency_id = $this->request->query('currency_id',null);
      $limit = $this->request->query('limit',30);
      $paginate = $this->request->query('paginate',0);
      $query = new StoreBankAccount();
      if(isset($store_id)){
         $query = $query->where('store_id',$store_id);
      }
      if(isset($currency_id)){
         $query = $query->where('bank_currency_id',$currency_id);
      }
      return ($paginate == 1)? $query->paginate($limit): $query->get();
   }

   
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $accounts = $this->onGetAccounts();
         return $this->successWithData($accounts);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   