<?php

namespace App\Http\Controllers\Api;

use App\Actions\City\GetCities;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request,$route_param = null){
        return (new GetCities($request,$route_param))->execute();
    }
}
