<?php
namespace App\Actions\StoreStaff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaff;
use App\Traits\HasRoles;
use App\Traits\HasStoreRoles;
use Illuminate\Validation\Rule;

class ChangeStaffPosition extends Action{
   use HasStoreRoles,HasRoles;
   protected $request,$user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id'=>['required','integer',Rule::exists('stores','id')->where(function($query){
            $query->where('user_id',$this->user->id);
         })],
         'staff_id'=>['required','integer',Rule::exists('store_staffs','id')->where(function($query){
            $query->where('store_id',$this->request->store_id);
         })],
         'new_position' => 'required|integer'
      ]);
      return $this->valResult($val);
   }

   protected function positionIsLegit(){
      $position = $this->request->new_position;
      if($this->inStoreStaffRoles($position) && $position != $this->getStoreOwnerRoleId()){
         return true;
      }
      return false;
   }

   protected function changeStaffPosition(){
      StoreStaff::where('id',$this->request->staff_id)
      ->where('store_id',$this->request->store_id)
      ->update(['staff_type'=>$this->request->new_position]);
   }

   
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if(!$this->positionIsLegit()) return $this->validationError('The new position you submitted is invalid');
         $this->changeStaffPosition();
         return $this->successMessage('Staff Position was successfully updated');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   