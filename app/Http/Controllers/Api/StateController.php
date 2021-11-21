<?php

namespace App\Http\Controllers\Api;

use App\Actions\State\GetStates;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request,$route_param = null){
        return (new GetStates($request,$route_param))->execute();
    }
}
