<?php
namespace App\Actions\StoreStaff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaff;
use App\Traits\HasStore;
use App\Traits\HasStoreRoles;
use Illuminate\Validation\Rule;

class RemoveStoreStaff extends Action{
   use HasStore,HasStoreRoles;

   protected $request,$user,$staff_id;
   public function __construct(Request $request,$staff_id){
      $this->request=$request;
      $this->staff_id = $staff_id;
      $this->user = $request->user();
   }

   protected function validate(){
      $data = $this->request->all();
      $data['staff_id'] = $this->staff_id;
      $val = Validator::make($data,[
         'store_id' => $this->storeIdValidationRule(),
         'staff_id' => ['required','integer',Rule::exists('store_staffs','id')->where(function($query){
            return $query->where('store_id',$this->request->query('store_id'));
         })]
      ]);
      return $this->valResult($val);
   }

   protected function getCurrentUserStaffAccount(){
      return $this->user->storeStaffAccounts()
      ->where('store_id',$this->request->store_id)->first();
   }

   protected function userIsEligible($store_staff){
      if($this->isStoreOwner($this->user->user_type)){
         return true;
      } else if($this->isStoreStaff($this->user->user_type)){
         if($this->isStoreManager($this->user->id,$this->request->store_id)){
            $current_user_store = $this->getCurrentUserStaffAccount();
            if($store_staff->staff_type < $current_user_store->staff_type){
               return true;
            }
         }
      }
      return false;
   }



   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $store_staff = StoreStaff::where('id',$this->staff_id)
         ->where('store_id',$this->request->query('store_id'))->first();
         if(!$this->userIsEligible($store_staff)) return $this->notAuthorized('You are not authorized to carry out this operation.');
         $store_staff->delete();
         return $this->successMessage('Staff account was removed successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   