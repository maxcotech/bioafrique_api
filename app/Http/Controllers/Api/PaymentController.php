<?php

namespace App\Http\Controllers\Api;

use App\Actions\Payment\InitializePayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request){
        return (new InitializePayment($request))->execute();
    }
}
