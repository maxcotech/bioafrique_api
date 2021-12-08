<?php
namespace App\Actions\SubOrder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\SubOrder;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use Illuminate\Validation\Rule;

class GetSubOrders extends Action{
   use HasRoles,HasStore;
   protected $request;
   protected $user;
   protected $sub_order_id;
   public function __construct(Request $request,$sub_order_id = null){
      $this->request = $request;
      $this->user = $request->user();
      $this->sub_order_id = $sub_order_id;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['authentication_type'] = $this->user->user_type;
      $data['sub_order_id'] = $this->sub_order_id;
      $val = Validator::make($data,[
         'order_id' => 'nullable|integer|exists:orders,id',
         'store_id' => $this->getStoreIdRules(),
         'status' => 'nullable|integer',
         'with_items' => 'nullable|integer',
         'user_id' => 'required_if:authentication_type,'.$this->getCustomerRoleId().'|integer|exists:users,id',
         'sub_order_id' => $this->getSubOrderIdRules(),
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getStoreIdRules(){
      if($this->isStoreStaff($this->user->user_type) || $this->isStoreOwner($this->user->user_type)){
         return $this->storeIdValidationRule();
      } else {
         return "nullable|integer|exists:stores,id";
      }
   }
   
   protected function getSubOrderIdRules(){
      $user_type = $this->user->user_type;
      if($this->isStoreStaff($user_type) || $this->isStoreOwner($user_type)){
         return ['nullable','integer',Rule::exists('sub_orders','id')->where(function($query){
            return $query->where('store_id',$this->request->query('store_id'));
         })];
      } else if($this->isCustomer($user_type)){
         return ['nullable','integer',Rule::exists('sub_orders','id')->where(function($query){
            return $query->where('user_id',$this->user->id);
         })];
      } else {
         return 'nullable|integer|exists:sub_orders,id';
      }
   }



   protected function onGetSubOrders(){
      $data = $this->getRelationshipArray();
      if($this->sub_order_id != null){
         return SubOrder::with($data)->where("id",$this->sub_order_id)->first();
      } else {
         $query = SubOrder::with($data);
         if($this->request->query('order_id',null) != null){
            $query = $query->where('order_id',$this->request->query('order_id'));
         } 
         if($this->request->query('store_id',null) != null){
            $query = $query->where('store_id',$this->request->query('store_id'));
         }
         if($this->request->query('status',null) != null){
            $query = $query->where('status',$this->request->query('status'));
         }
         if($this->isCustomer($this->user->user_type)){
            $query = $query->where('user_id',$this->user->id);
         } else {
            if($this->request->query('user_id',null) != null){
               $query = $query->where('user_id',$this->request->query('user_id'));
            }
         }
        
         return $query->paginate($this->request->query('limit',10));
      }
   }

   protected function getRelationshipArray(){
      $data = [];
      $user_type = $this->user->user_type;
      if($this->request->query('with_items',0) == 1){
         $data = [
            'items:id,user_id,paid_amount,quantity,product_type,sub_order_id,product_id,variation_id',
            'items.product:id,product_name,product_image,product_sku',
            'items.variation:id,variation_name,variation_image',
         ];
      }
      if(!$this->isCustomer($user_type)){
         array_push($data,'user:id,first_name,last_name,email,phone_number,telephone_code,account_status');
      }
      if($this->isCustomer($user_type) && $this->request->query('user_id') != null){
         array_push($data,'fundLockPassword:id,lock_password,status,sub_order_id,user_id');
      }
      if($this->isStoreStaff($user_type) || $this->isStoreOwner($user_type) || $this->isCustomer($user_type)){
         array_push($data,'order:id,user_id,order_number,total_amount,status,billing_address_id');
         array_push($data,'order.billingAddress:id,city_id,state_id,country_id');
         array_push($data,'order.billingAddress.state:id,state_name');
         array_push($data,'order.billingAddress.city:id,city_name');
         array_push($data,'order.billingAddress.country:id,country_name');
      }
      return $data;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->onGetSubOrders();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   