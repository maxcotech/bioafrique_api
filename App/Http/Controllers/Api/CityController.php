<?php

namespace App\Http\Controllers\Api;

use App\Actions\City\CreateCity;
use App\Actions\City\DeleteCity;
use App\Actions\City\GetCities;
use App\Actions\City\UpdateCity;
use App\Actions\City\UpdateCityStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request,$route_param = null){
        return (new GetCities($request,$route_param))->execute();
    }
    public function create(Request $request){
        return (new CreateCity($request))->execute();
    }
    public function updateStatus(Request $request){
        return (new UpdateCityStatus($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateCity($request))->execute();
    }
    public function delete(Request $request,$city_id){
        return (new DeleteCity($request,$city_id))->execute();
    }
}
