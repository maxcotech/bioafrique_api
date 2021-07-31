<?php

namespace App\Http\Controllers\Api;

use App\Actions\Store\CreateStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function create(Request $request){
        return (new CreateStore($request))->execute();
    }
}
