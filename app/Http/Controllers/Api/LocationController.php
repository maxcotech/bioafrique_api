<?php

namespace App\Http\Controllers\Api;

use App\Actions\Location\PopulateLocations;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function populateLocations(Request $request){
        return (new PopulateLocations($request))->execute();
    }
}
