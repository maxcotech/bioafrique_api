<?php
namespace App\Services;

class PaymentService{
    public $gateway_ref = null;
    public $gateway_type = null;
    public function __construct(string $gateway,$ref = null){
       $this->gateway_type = $gateway;
       $this->gateway_ref = $ref;
    }

    public function verifyTransaction(){
        //to be implemented
    }

    protected function getSecretKey(string $gateway){
        //to be implemented 
    }

}
    