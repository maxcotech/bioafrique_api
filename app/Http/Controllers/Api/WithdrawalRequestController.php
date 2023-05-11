<?php

namespace App\Http\Controllers\Api;

use App\Actions\WithdrawalRequest\CreateRequest;
use App\Actions\WithdrawalRequest\GetWithdrawalRequests;
use App\Actions\WithdrawalRequest\MassSettle;
use App\Actions\WithdrawalRequest\Settle;
use App\Actions\WithdrawalRequest\UpdateRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithdrawalRequestController extends Controller
{
    public function index(Request $request){
        return (new GetWithdrawalRequests($request))->execute();
    }

    public function settle(Request $request){
        return (new Settle($request))->execute();
    }

    public function massSettle(Request $request){
        return (new MassSettle($request))->execute();
    }

    public function create(Request $request){
        return (new CreateRequest($request))->execute();
    }

    public function updateStatus(Request $request){
        return (new UpdateRequestStatus($request))->execute();
    }
}
