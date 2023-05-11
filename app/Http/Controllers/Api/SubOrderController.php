<?php

namespace App\Http\Controllers\Api;

use App\Actions\SubOrder\GetSubOrders;
use App\Actions\SubOrder\UpdateSubOrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubOrderController extends Controller
{
    public function index(Request $request,$sub_order_id = null){
        return (new GetSubOrders($request,$sub_order_id))->execute();
    }
    public function updateStatus(Request $request){
        return (new UpdateSubOrderStatus($request))->execute();
    }
}
