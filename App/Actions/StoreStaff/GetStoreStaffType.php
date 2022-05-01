<?php
namespace App\Actions\StoreStaff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\StoreStaff;
use App\Traits\HasStoreRoles;
use App\Traits\StringFormatter;

class GetStoreStaffType extends Action{
   use StringFormatter, HasStoreRoles;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'user_id' => 'nullable|integer|exists:users,id',
         'store' => 'required|integer|exists:stores,id'
      ]);
      return $this->valResult($val);
   }

   protected function getStoreStaff(){
      $query = StoreStaff::where('store_id',$this->request->query('store'));
      if($this->request->query('user_id',null)){
         $query->where('user_id',$this->request->query('user_id'));
      } else {
         if($this->request->user()){
            $user = $this->request->user();
            $query->where('user_id',$user->id);
         }
      }
      return $query->first(['staff_type']);
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != 'success') return $this->resp($val);
         $store_staff = $this->getStoreStaff();
         if(isset($store_staff)){
            return $this->successWithData([
               'staff_type' => $store_staff->staff_type,
               'staff_type_text' => $this->capitalizeByDelimiter($this->getStoreRoleText($store_staff->staff_type),"_")
            ]);
         }
         return $this->validationError('Invalid query data.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   