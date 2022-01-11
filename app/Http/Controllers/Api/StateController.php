<?php

namespace App\Http\Controllers\Api;

use App\Actions\State\CreateState;
use App\Actions\State\DeleteState;
use App\Actions\State\GetStates;
use App\Actions\State\UpdateState;
use App\Actions\State\UpdateStateStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request,$route_param = null){
        return (new GetStates($request,$route_param))->execute();
    }
    public function create(Request $request){
        return (new CreateState($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateState($request))->execute();
    }
    public function updateStatus(Request $request){
        return (new UpdateStateStatus($request))->execute();
    }
    public function delete(Request $request,$state_id){
        return (new DeleteState($request,$state_id))->execute();
    }
}
