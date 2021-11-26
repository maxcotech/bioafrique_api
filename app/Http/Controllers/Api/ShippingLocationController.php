<?php

namespace App\Http\Controllers\Api;

use App\Actions\ShippingLocation\CreateShippingLocation;
use App\Actions\ShippingLocation\DeleteShippingLocation;
use App\Actions\ShippingLocation\GetShippingLocations;
use App\Actions\ShippingLocation\UpdateShippingLocation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShippingLocationController extends Controller
{
    public function create(Request $request){
        return (new CreateShippingLocation($request))->execute();
    }
    public function index(Request $request){
        return (new GetShippingLocations($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateShippingLocation($request))->execute();
    }
    public function delete(Request $request,$location_id){
        return (new DeleteShippingLocation($request,$location_id))->execute();
    }
}
