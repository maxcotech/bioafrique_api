<?php
namespace App\Actions\City;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\City;
use App\Traits\HasRoles;

class DeleteCity extends Action{
    use HasRoles;
    protected $request;
    protected $city_id;
    public function __construct(Request $request,$city_id){
        $this->request=$request;
        $this->city_id = $city_id;
    }

    public function execute(){
        try{
            if($this->isSuperAdmin()){
                City::where('id',$this->city_id)->delete();
                return $this->successMessage("City deleted successfully.");
            }
            return $this->notAuthorized("You are not authorized.");
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }

}
    