<?php
namespace App\Actions\BillingAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\BillingAddress;
use App\Traits\HasRoles;

class GetCurrentBillingAddress extends Action{
   use HasRoles;
   protected $request,$user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'user_id' => $this->getUserIdRules()
      ]);
      return $this->valResult($val);
   }

   protected function appendRelationships($query){
      return $query->with([
         'country:id,country_name',
         'state:id,state_name',
         'city:id,city_name'
      ]);
   }

   protected function getCurrentBillingAddress(){
      $query = BillingAddress::where('is_current',BillingAddress::$current_id);
      if($this->isCustomer($this->user->user_type)){
         $query = $query->where('user_id',$this->user->id);
      } else {
         $query = $query->where('user_id',$this->request->query('user_id'));
      }
      $query = $this->appendRelationships($query);
      return $query->first();
   }

   protected function getUserIdRules(){
      if($this->isCustomer($this->user->user_type)){
         return 'nullable|integer';
      } else {
         return 'required|integer|exists:users,id';
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->getCurrentBillingAddress();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   