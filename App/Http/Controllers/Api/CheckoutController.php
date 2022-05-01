<?php

namespace App\Http\Controllers\Api;

use App\Actions\Checkout\GetCheckoutData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request){
        return (new GetCheckoutData($request))->execute();
    }
}
