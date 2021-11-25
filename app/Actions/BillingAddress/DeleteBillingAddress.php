<?php

namespace App\Actions\BillingAddress;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\BillingAddress;
use Illuminate\Support\Facades\DB;

class DeleteBillingAddress extends Action
{
   protected $request;
   protected $id;
   protected $user;
   public function __construct(Request $request, int $id){
      $this->request = $request;;
      $this->id = $id;
      $this->user = $request->user();
   }

   protected function makeAnotherAddressCurrent(){
      $next_current_address = BillingAddress::where('user_id',$this->user->id)
      ->where('id','!=',$this->id)->first();
      if(isset($next_current_address)){
         $next_current_address->update([
            'is_current' => BillingAddress::$current_id
         ]);
      }
   }



   protected function onDelete(){
      $address = BillingAddress::where('user_id', $this->user->id)
      ->where('id', $this->id)->first();
      DB::transaction(function () use ($address) {
         if (isset($address)) {
            if ($address->is_current == BillingAddress::$current_id) {
               $this->makeAnotherAddressCurrent();
            }
            $address->delete();
         }
      });
   }


   public function execute()
   {
      try {
         $this->onDelete();
         return $this->successMessage('Billing Address deleted successfully.');
      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
}
