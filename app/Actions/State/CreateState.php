<?php
namespace App\Actions\State;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\State;
use App\Traits\HasResourceStatus;
use App\Traits\HasRoles;
use Illuminate\Validation\Rule;

class CreateState extends Action{
   use HasRoles,HasResourceStatus;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'country_id' => 'required|integer|exists:countries,id',
         'state_code' => ['nullable','string',Rule::unique('states','state_name')->where(function($query){
            $query->where('country_id',$this->request->country_id);
         })],
         'state_name' => ['required','string',Rule::unique('states','state_name')->where(function($query){
            $query->where('country_id',$this->request->country_id);
         })]
      ]);
      return $this->valResult($val);
   }

   protected function getNewStatus(){
      if($this->isSuperAdmin()){
         return $this->getResourceActiveId();
      }
      return $this->getResourceInReviewId();
   }

   protected function onCreate(){
      State::create([
         'country_id' => $this->request->country_id,
         'state_code' => $this->request->state_code,
         'state_name' => $this->request->state_name,
         'status' => $this->getNewStatus()
      ]);
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $this->onCreate();
         return $this->successMessage('Successfully created state.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   