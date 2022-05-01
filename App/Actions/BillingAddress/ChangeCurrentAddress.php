<?php
namespace App\Actions\BillingAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\BillingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChangeCurrentAddress extends Action{
   protected $request;
   protected $address_id;
   protected $user;
   public function __construct(Request $request,$address_id){
      $this->request=$request;
      $this->address_id = $address_id;
      $this->user = $request->user();
   }

   protected function validate(){
      $data = ['billing_address_id' => $this->address_id];
      $val = Validator::make($data,[
         'billing_address_id' => ['required','integer',Rule::exists('billing_addresses','id')
         ->where(function($query){
            return $query->where('user_id',$this->user->id);
         })]
      ]);
      return $this->valResult($val);
   }


   protected function onChangeCurrentAddress(){
      DB::transaction(function(){
         $address = BillingAddress::where('user_id',$this->user->id)
         ->where('id',$this->address_id)->first();
         if(isset($address)){
            if($address->is_current != BillingAddress::$current_id){
               //change status of any other current status;
               BillingAddress::where('user_id',$this->user->id)
               ->update(['is_current'=>BillingAddress::$not_current_id]);
               $address->update(['is_current' => BillingAddress::$current_id]);
            }
         }
      });
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $this->onChangeCurrentAddress();
         return $this->successMessage('Current Billing Address updated successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   