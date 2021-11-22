<?php
namespace App\Actions\ShippingGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShippingGroup;
use App\Traits\HasStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeleteShippingGroup extends Action{
   use HasStore;

   protected $request;
   protected $group_id;
   public function __construct(Request $request,int $group_id){
      $this->request=$request;
      $this->group_id = $group_id;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['group_id'] = $this->group_id;
      $val = Validator::make($data,[
         'store_id' => $this->storeIdValidationRule(),
         'group_id' => ['required','integer',Rule::exists('shipping_groups','id')
         ->where(function($query){
            $query->where('store_id',$this->request->query('store_id'));
         })]
      ]);
      return $this->valResult($val);
   }

   protected function onDeleteShippingGroup(){
      $group = ShippingGroup::where('store_id',$this->request->query('store_id'))
      ->where('id',$this->group_id)->first();
      if(isset($group)){
         DB::transaction(function()use($group){
            $group->shippingLocations()->delete();
            $group->delete();
         });
      }
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $this->onDeleteShippingGroup();
         return $this->successMessage('Shipping Group Deleted Successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   