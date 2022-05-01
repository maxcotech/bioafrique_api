<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class BankServices{
    public function __construct(){
       //
    }

    public function getSecretKey(){
        return env('FLUTTERWAVE_SK');
    }

    public function getBankCodes($currency_code){
        $base_url = env('FLUTTERWAVE_BASE_URL');
        $url = $base_url."banks/".$currency_code;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->getSecretKey()
        ])->get($url);
        return json_decode($response->body());
    }

}
    