<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Cookie\CreateCookie;

class CookieController extends Controller
{
    public function create(Request $request){
        return (new CreateCookie ($request))->execute();
    }
   
}
