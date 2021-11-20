<?php
namespace App\Actions\StoreStaff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Store;
use App\Traits\HasStore;

class GetStoreStaffs extends Action{
   use HasStore;

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'store_id' => $this->storeIdValidationRule(),
         'status' => "nullable|integer",
         'staff_type' => "nullable|integer"
      ]);
      return $this->valResult($val);
   }

   protected function filterStaffs($query){
      $staffQuery = $query;
      if($this->request->query('status',null) != null){
         $staffQuery = $staffQuery->where('status',$this->request->query('status'));
      }
      if($this->request->query('staff_type',null) != null){
         $staffQuery = $staffQuery->where('staff_type',$this->request->query('staff_type'));
      }
      return $staffQuery;
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $store = Store::where('id',$this->request->query('store_id'))->first();
         $staffQuery = $store->staffs();
         $staffQuery = $staffQuery->with(['user:id,first_name,last_name,email,phone_number']);
         $staffQuery = $this->filterStaffs($staffQuery);
         $data = $staffQuery->orderBy('id','desc')->paginate(15,['id','store_id','user_id','staff_type','status']);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   