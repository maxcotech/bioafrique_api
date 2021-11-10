<?php

namespace App\Http\Controllers\Api;

use App\Actions\VariationOption\CreateOption;
use App\Actions\VariationOption\GetOptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VariationOptionsController extends Controller
{
    public function create(Request $request){
        return (new CreateOption($request))->execute();
    }

    public function index(Request $request){
        return (new GetOptions($request))->execute();
    }
}
