<?php

namespace App\Http\Controllers\Api;

use App\Actions\Admin\CreditWallet;
use App\Actions\Admin\DebitWallet;
use App\Actions\Admin\GetWallet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getWallet(Request $request){
        return (new GetWallet($request))->execute();
    }
    public function debitWallet(Request $request){
        return (new DebitWallet($request))->execute();
    }
    public function creditWallet(Request $request){
        return (new CreditWallet($request))->execute();
    }
}
