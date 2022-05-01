<?php
namespace App\Actions\Country;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Country;
use App\Models\State;
use App\Traits\FilePath;
use App\Traits\HasFile;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class DeleteCountry extends Action{
   use HasRoles,HasFile,FilePath;
   protected $request;
   protected $country_id;
   public function __construct(Request $request,$country_id){
      $this->request=$request;
      $this->country_id = $country_id;
   }

   protected function deleteStates($country_id){
      $states = State::where('country_id',$country_id)->get();
      if(count($states) > 0){
         foreach($states as $state){
            $state->cities()->delete();
            $state->delete();
         }
      }
   }


   public function execute(){
      try{
         if($this->isSuperAdmin()){
            $country = Country::find($this->country_id);
            if(isset($country)){
               DB::transaction(function()use($country){
                  $this->deleteFile($this->getInitialPath(
                     $country->country_logo,
                     CreateCountry::uploadPath
                  ));
                  $this->deleteStates($country->id);
                  $country->delete();
               });
            }
            return $this->successMessage('Country deleted successfully.');
         }
         return $this->notAuthorized("You are not authorized.");
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   