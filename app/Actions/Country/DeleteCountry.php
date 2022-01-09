<?php
namespace App\Actions\Country;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Country;
use App\Traits\HasRoles;

class DeleteCountry extends Action{
   use HasRoles;
   protected $request;
   protected $country_id;
   public function __construct(Request $request,$country_id){
      $this->request=$request;
      $this->country_id = $country_id;
   }
   public function execute(){
      try{
         if($this->isSuperAdmin()){
            Country::where('id',$this->country_id)->delete();
            return $this->successMessage('Country deleted successfully.');
         }
         return $this->notAuthorized("You are not authorized.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   