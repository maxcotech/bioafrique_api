<?php

namespace App\Http\Controllers\Api;

use App\Actions\Admin\GetWallet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getWallet(Request $request){
        return (new GetWallet($request))->execute();
    }
}
