<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreFund\GetWallet;
use App\Actions\StoreFund\WithdrawFund;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreFundController extends Controller
{
    public function withdrawFund(Request $request){
        return (new WithdrawFund($request))->execute();
    }
    public function getWallet(Request $request){
        return (new GetWallet($request))->execute();
    }
}
