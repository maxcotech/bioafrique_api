<?php
namespace App\Actions\OrderItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\OrderItem;
use App\Traits\HasRoles;

class GetOrderItems extends Action{
   use HasRoles;
   protected $request;
   protected $user;
   protected $order_item_id;
   public function __construct(Request $request,$order_item_id = null){
      $this->request=$request;
      $this->user = $request->user();
      $this->order_item_id = $order_item_id;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['order_item_id'] = $this->order_item_id;
      $val = Validator::make($this->request->all(),[
         'order_id' => $this->getOrderIdRules(),
         'sub_order_id' => 'nullable|integer|exists:sub_orders,id',
         'user_id' => 'nullable|integer|exists:users,id',
         'order_item_id' => 'nullable|integer|exists:order_items,id',
         'limit' => 'nullable|integer|min:1',
         'paginate' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getOrderIdRules(){
      if($this->order_item_id == null){
         return "required_if:sub_order_id,null|integer|exists:orders,id";
      } else {
         return "nullable|integer|exists:orders,id";
      }
   }

   protected function onGetOrderItems(){
      $user_type = $this->user->user_type;
      $with_data = $this->getRelationshipArray();
      if($this->order_item_id != null){
         return $this->getOrderItem($user_type,$with_data);
      } else {
         return $this->getOrderItems($user_type,$with_data);
      }
   }

   protected function getOrderItems($user_type,$data){
      $query = new OrderItem();
      if($this->request->query('sub_order_id',null) != null){
         $query = $query->with($data)->where('sub_order_id',$this->request->query('sub_order_id'));
      } elseif($this->request->query('order_id',null) != null) {
         $query = $query->with($data)->where('order_id',$this->request->query('order_id'));
      } else {
         $query = $query->with($data);
      }
      if($this->isCustomer($user_type)){
         $query = $query->where('user_id',$this->user->id);
      } elseif ($this->request->query('user_id',null) != null) {
         $query = $query->where('user_id',$this->request->query('user_id'));
      }
      return (isset($query))? $this->getQueryData($query) : null;
   }

   protected function getQueryData($query){
      if($this->request->query('paginate',1) == 1){
         return $query->paginate($this->request->query('limit',15));
      } else {
         return $query->get();
      }
   }

   protected function getRelationshipArray(){
      $data = [
         'product:id,product_image,product_name,product_image,product_sku',
         'variation:id,variation_name,variation_image,variation_sku',
      ];
      return $data;
   }

   protected function getOrderItem($user_type,$data){
      $query =  OrderItem::with($data)->where('id',$this->order_item_id);
      if($this->isCustomer($user_type)){
         $query = $query->where('user_id',$this->user->id);
      }
      return $query->first();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->onGetOrderItems();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   