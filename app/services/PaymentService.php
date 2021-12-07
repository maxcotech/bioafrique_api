<?php
namespace App\Services;

use App\Models\OrderTransaction;
use App\Traits\HasPayment;
use Illuminate\Support\Facades\Http;

class PaymentService{
    use HasPayment;
    public $amount = null;
    public $gateway_type = null;
    public $site_ref = null;
    public $currency = null;
    public function __construct(string $gateway,string $site_ref,string $curr_code, $amount){
       $this->gateway_type = $gateway;
       $this->site_ref = $site_ref;
       $this->currency_code = $curr_code;
       $this->amount = round($amount,2);
    }

    public function verifyPayment($transaction_id = null){
        if($this->gateway_type == OrderTransaction::FLUTTERWAVE && isset($transaction_id)){
            return $this->verifyFlutterwavePayment($transaction_id);
        }
        if($this->gateway_type == OrderTransaction::PAYSTACK){
            return $this->verifyPaystackPayment();
        }
        return false;
    }

    protected function verifyPaystackPayment(){
        $base_url = env('PAYSTACK_BASE_URL');
        $verify_url = $base_url."transaction/verify/".$this->site_ref;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getSecretKey()
        ])->get($verify_url);
        if($response->successful()){
            $result = json_decode($response->body());
            if($result->status == true && $result->data->status == "success"){
                $paid_amount = round($result->data->amount,2);
                if($paid_amount >= $this->amount && $this->currency_code == $result->data->currency){
                    return true;
                }
            }
        }
        return false;
    }

    protected function verifyFlutterwavePayment($transaction_id){
        $base_url = env('FLUTTERWAVE_BASE_URL');
        $verify_url = $base_url."transactions/".$transaction_id."/verify";
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->getSecretKey()
        ])->get($verify_url);
        if($response->successful()){
            $result = json_decode($response->body());
            if($result->status == "success" && $result->data->status == "successful"){
                $paid_amount = round($result->data->amount,2);
                if($paid_amount >= $this->amount && $this->currency_code == $result->data->currency){
                    return true;
                }
            }
        }
        return false;
    }

    protected function getSecretKey(){
        return $this->getGatewayPrivateKey($this->gateway_type);
    }

}
    