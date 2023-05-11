<?php

namespace App\Http\Controllers\Api;

use App\Actions\Admin\CreditWallet;
use App\Actions\Admin\DebitWallet;
use App\Actions\Admin\GetDashboardData;
use App\Actions\Admin\GetPreferences;
use App\Actions\Admin\GetWallet;
use App\Actions\Admin\UpdatePreferences;
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
    public function getDashboardData(Request $request){
        return (new GetDashboardData($request))->execute();
    }
    public function getAdminPreferences(Request $request){
        return (new GetPreferences($request))->execute();
    }
    public function updateAdminPreferences(Request $request){
        return (new UpdatePreferences($request))->execute();
    }
}
