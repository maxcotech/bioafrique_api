<?php
namespace App\Actions\SubOrder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Order;
use App\Models\OrderFundLock;
use App\Models\SubOrder;
use App\Traits\HasEncryption;
use App\Traits\HasRoles;
use App\Traits\HasStore;

class UpdateSubOrderStatus extends Action{
   use HasRoles,HasStore,HasEncryption;
   protected $request;
   protected $user;
   protected $user_type;
   public function __construct(Request $request){
      $this->request = $request;
      $this->user = $request->user();
      $this->user_type = $this->user->user_type;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'new_status' => 'required|integer',
         'store_id' => $this->getStoreIdRules(),
         'user_id' => 'nullable|integer|exists:users,id',
         'sub_order_id' => 'required|integer|exists:sub_orders,id',
         'fund_password' => $this->getFundPasswordRules(),
      ]);
      return $this->valResult($val);
   }

   protected function getStoreIdRules(){
      if($this->isStoreOwner($this->user_type) || $this->isStoreStaff($this->user_type)){
         return $this->storeIdValidationRule();
      } else {
         return 'nullable|integer|exists:stores,id';
      }
   }

   protected function getFundPasswordRules(){
      if($this->isStoreOwner($this->user_type) || $this->isStoreStaff($this->user_type)){
         return 'required|string';
      } elseif ($this->isSuperAdmin($this->user_type)){
         return 'nullable|string';
      } elseif ($this->isCustomer($this->user_type)){
         $sub_order = SubOrder::find($this->request->sub_order_id);
         if(isset($sub_order) && $sub_order->user_id == $this->user->id){
            return 'nullable|string';
         }
      }
      throw new \Exception('You are not authorized to carry out this request.');

   }


   protected function userCanChangeOrderStatus($sub_order){
      $new_status = $this->request->new_status;
      if($new_status == $sub_order->status){
         return $this->boolMessage("This order's current status is equal to submitted status",true);
      }
      if(!$this->isStoreOwner($this->user_type) && !$this->isStoreStaff($this->user_type) && !$this->isSuperAdmin($this->user_type) && !$this->isCustomer($this->user_type)){
         return $this->boolMessage('You are not authorized to make this request',true);
      }
      if($sub_order->status == Order::STATUS_COMPLETED){
         return $this->boolMessage('You can not change the status of already completed order.',true);
      }
      if($this->isStoreStaff($this->user_type) || $this->isStoreOwner($this->user_type)){
         if($new_status == Order::STATUS_COMPLETED){
            $lock_model = $sub_order->fundLockPassword;
            $encrypted = $lock_model->lock_password;
            $decrypted = null;
            if($this->user->id == $sub_order->id){
               $decrypted = $encrypted;
            } else {
               $decrypted = $this->decryptData($encrypted,$sub_order->user_id);
            }
            if($decrypted != $this->request->lock_password){
               return $this->boolMessage("The fund lock password you entered is incorrect",true);
            }
         }
      }
      return $this->boolMessage('successful',false);
   }

   protected function unlockFunds($sub_order){
      $fund = $sub_order->fundLockPassword;
      if(isset($fund)){
         if($fund->status == OrderFundLock::STATUS_LOCKED){
            $fund->update(['status'=>OrderFundLock::STATUS_OPENED]);
         }
      }
   }

   protected function onChangeOrderStatus($sub_order){
      $sub_order->update(['status'=>$this->request->new_status]);
      if($this->request->new_status == Order::STATUS_COMPLETED){
         $this->unlockFunds($sub_order);
      }
   }

   protected function sendNotificationsAndEmails($sub_order){
      //send necessary emails and notifications 
   }

   protected function getSubOrderByInputs(){
      $query = SubOrder::where('id',$this->request->sub_order_id);
      if($this->isStoreStaff($this->user_type) || $this->isStoreOwner($this->user_type)){
         $query = $query->where('store_id',$this->request->store_id);
      }
      if($this->request->input('user_id',null) != null){
         $query = $query->where('user_id',$this->request->input('user_id'));
      }
      return $query->first();
   }

   protected function updateMainOrderWhenPossible($sub_order){
      if(!SubOrder::where('order_id',$sub_order->order_id)
         ->where('status','!=',$this->request->new_status)->exists()){
         Order::where('id',$sub_order->id)
         ->update([
            'status'=>$this->request->new_status
         ]);
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $sub_order = $this->getSubOrderByInputs();
         if(!isset($sub_order)) return $this->internalError('Failed to complete order status update.');
         $result = $this->userCanChangeOrderStatus($sub_order);
         if($result['error'] == true) return $this->validationError($result['message']);
         $this->onChangeOrderStatus($sub_order);
         $sub_order->refresh();
         $this->updateMainOrderWhenPossible($sub_order);
         $this->sendNotificationsAndEmails($sub_order);
         return $this->successMessage('Order status was updated successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   