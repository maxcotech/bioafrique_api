<?php
namespace App\Actions\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Traits\HasAuthStatus;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;

class GetCities extends Action{
   use HasResourceStatus,HasRoles,HasAuthStatus;

   protected $request;
   protected $route_param;

   public function __construct(Request $request,$route_param = null){
      $this->request=$request;
      $this->route_param = $route_param;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'state_id' => 'nullable|integer|exists:states,id',
         'state' => 'required_if:state_id,null|string|exists:states,state_name',
         'country_id' => 'required_if:state_id,null|integer|exists:countries,id',
         'status' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getState(){
      if($this->request->query('state_id',null) != null){
         return State::where('id',$this->request->query('state_id',null))->first();
      } else {
         return State::where('state_name',$this->request->query('state'))
         ->where('country_id',$this->request->query('country_id'))->first();
      }
   }

   protected function getCities($state,$access_type){
      $query = City::where('state_id',$state->id);
      if($access_type->type == User::auth_type){
         if($this->isSuperAdmin()){
            if($this->request->query('status',null) != null){
               $query = $query->where('status',$this->request->query('status',null));
            }
         } else {
            $query = $query->where('status',$this->getResourceActiveId());
         }
      } else {
         $query = $query->where('status',$this->getResourceActiveId());
      }
      $query = $query->orderBy('city_name','asc');
      if($this->route_param === "paginate"){
         return $query->paginate(15,['id','city_name','city_code','state_id','status']);
      }
      return $query->select(['id','city_name','city_code','state_id','status'])->get();

   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $state = $this->getState();
         $access_type = $this->getUserAuthTypeObject();
         $cities = $this->getCities($state,$access_type);
         return $this->successWithData($cities);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   