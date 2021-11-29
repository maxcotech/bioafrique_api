<?php
namespace App\Traits;

use App\Models\OrderTransaction;

trait HasPayment{
    public $gateways = [
        OrderTransaction::FLUTTERWAVE => "Flutterwave",
        OrderTransaction::PAYSTACK => "Paystack"
    ];
    public $payment_status_list = [
        OrderTransaction::STATUS_COMPLETED => "Completed",
        OrderTransaction::STATUS_PENDING => "Pending"
    ];

    public function getGatewayPublicKey($gateway){
        switch($gateway){
            case OrderTransaction::FLUTTERWAVE: return env('FLUTTERWAVE_PK');
            case OrderTransaction::PAYSTACK: return env('PAYSTACK_PK');
            default: return null;
        }
    }

    public function getGatewayPrivateKey($gateway){
        switch($gateway){
            case OrderTransaction::FLUTTERWAVE: return env('FLUTTERWAVE_SK');
            case OrderTransaction::PAYSTACK: return env('PAYSTACK_SK');
            default: return null;
        }
    }

}
    