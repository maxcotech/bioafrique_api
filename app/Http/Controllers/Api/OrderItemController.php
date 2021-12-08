<?php

namespace App\Http\Controllers\Api;

use App\Actions\OrderItem\GetOrderItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index(Request $request,$order_item_id = null){
        return (new GetOrderItems($request,$order_item_id))->execute();
    }
}
