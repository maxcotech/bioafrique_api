<?php
namespace App\Actions\State;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\State;
use App\Models\User;
use App\Traits\HasAuthStatus;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

class GetStates extends Action{
   use HasRoles,HasResourceStatus,HasAuthStatus;

   protected $request;
   protected $route_param;
   public function __construct(Request $request,$route_param = null){
      $this->request=$request;
      $this->route_param = $route_param;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'country_id' => 'required|integer|exists:countries,id',
         'status' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getStates($access_type){
      $query = State::where('country_id',$this->request->query('country_id'));
      $select_fields = ['id','state_name','state_code','country_id','status'];
      if($access_type->type == User::auth_type){
         if($this->isSuperAdmin()){
            if($this->request->query('status',null) != null){
               Log::alert("status is ".$this->request->query('status'));
               $query = $query->where('status',$this->request->query('status',null));
            }
         } else {
            $query = $query->where('status',$this->getResourceActiveId());
         }
      } else {
         $query = $query->where('status',$this->getResourceActiveId());
      }
      if($this->route_param === "paginate"){
         return $query->paginate(15,$select_fields);
      }
      return $query->select($select_fields)->get();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $access_type = $this->getUserAuthTypeObject();
         $states = $this->getStates($access_type);
         return $this->successWithData($states);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   