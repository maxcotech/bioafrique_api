<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cookie\CheckCookie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Cookie\CreateCookie;

class CookieController extends Controller
{
    public function create(Request $request){
        return (new CreateCookie ($request))->execute();
    }
    public function checkCookie(Request $request){
        return (new CheckCookie($request))->execute();
    }
   
}
