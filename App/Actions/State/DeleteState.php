<?php
namespace App\Actions\State;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\State;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class DeleteState extends Action{
use HasRoles;
protected $request;
protected $state_id;
public function __construct(Request $request,$state_id){
    $this->request=$request;
    $this->state_id = $state_id;
}

public function onDelete(){
    $state = State::find($this->state_id);
    if(isset($state)){
        DB::transaction(function () use($state) {
            $state->cities()->delete();
            $state->delete();        
        });
    }
}

public function execute(){
    try{
        if($this->isSuperAdmin()){
            $this->onDelete();
            return $this->successMessage("State deleted successfully");
        }
        return $this->notAuthorized('You are not authorized.');
    }
    catch(\Exception $e){
        return $this->internalError($e->getMessage());
    }
}

}
    