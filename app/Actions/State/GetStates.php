<?php
namespace App\Actions\State;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\State;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;

class GetStates extends Action{
   use HasRoles,HasResourceStatus;

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

   protected function getStates(){
      $query = State::where('country_id',$this->request->query('country_id'));
      $select_fields = ['id','state_name','state_code','country_id'];
      if($this->request->query('status',null) != null && $this->isSuperAdmin()){
         $query = $query->where('status',$this->request->query('status',null));
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
         $states = $this->getStates();
         return $this->successWithData($states);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   