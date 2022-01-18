<?php

namespace App\Http\Controllers\Api;

use App\Actions\Payment\InitializePayment;
use App\Actions\Payment\VerifyPayment;
use App\Http\Controllers\Controller;
use App\Models\OrderTransaction;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use HasHttpResponse;
    public function create(Request $request){
        return (new InitializePayment($request))->execute();
    }

    public function verifyPayment(Request $request){
        return (new VerifyPayment($request))->execute();
    }

    public function getPaymentMethods(){
        $methods = [
            [
                'method_id' => OrderTransaction::FLUTTERWAVE,
                'method_name' => "Flutterwave"
            ],
            [
                'method_id' => OrderTransaction::PAYSTACK,
                'method_name' => "Paystack"
            ]
        ];
        return $this->successWithData($methods);
    }
}
