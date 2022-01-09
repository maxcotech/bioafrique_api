<?php
namespace App\Actions\Country;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Country;
use App\Traits\HasRoles;
use Illuminate\Validation\Rule;

class UpdateCountry extends Action{
   use HasRoles;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:countries,id',
         'country_code' => ['required','string',Rule::unique('countries','country_code')->where(function($query){
            $query->where('id','!=',$this->request->id);
         })],
         'country_name' => ['required','string',Rule::unique('countries','country_name')->where(function($query){
            $query->where('id','!=',$this->request->id);
         })],
         'country_tel_code' => 'required|string',
      ]);
      return $this->valResult($val);
   }

   protected function onUpdateCountry(){
      Country::where('id',$this->request->id)
      ->update($this->request->all());
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->isSuperAdmin()){
            $this->onUpdateCountry();
            return $this->successMessage('Country updated successfully.');
         } else {
            return $this->notAuthorized('You are not authorized');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   