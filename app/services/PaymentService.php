<?php
namespace App\Services;

use App\Exceptions\InvalidBankDetails;
use App\Exceptions\InvalidTransferGateway;
use App\Exceptions\TransferFailed;
use App\Models\OrderTransaction;
use App\Traits\HasPayment;
use App\Services\Utilities\BankPayload;
use Illuminate\Support\Facades\Http;

class PaymentService{
    use HasPayment;
    public $amount = null;
    public $gateway_type = null;
    public $site_ref = null;
    public $currency_code = null;
    public function __construct(int $gateway,string $site_ref,string $currency_code, $amount){
       $this->gateway_type = $gateway;
       $this->site_ref = $site_ref;
       $this->currency_code = $currency_code;
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

    public function transferFunds(BankPayload $payload,$narration = "Vendor settlement"){
       switch($this->gateway_type){
           case OrderTransaction::FLUTTERWAVE: return $this->flutterwaveTransfer($payload,$narration);
           default: throw new InvalidTransferGateway("The selected gateway is not supported for bank transfers.");
       }
    }

    protected function flutterwaveTransfer(BankPayload $payload,$narration = null){
        if($payload->account_number !== null && $payload->bank_code !== null && $payload->bank_currency_code !== null){
            $base_url = env('FLUTTERWAVE_BASE_URL');
            $transfer_url = $base_url."transfers";
            $request_data = [
                'account_bank' => $payload->bank_code,
                'account_number' => $payload->account_number,
                'amount' => $this->amount,
                'narration' => $narration ?? "Vendor Settlement",
                'currency' => $this->currency_code,
                'reference' => $this->site_ref,
                'debit_currency' => $payload->bank_currency_code
            ];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$this->getSecretKey()
            ])->post($transfer_url,$request_data);
            $data = json_decode($response->body());
            if($response->ok()){
                if($data->status === "success"){
                    return true;
                } else {
                    throw new TransferFailed($data->message);
                }
            } else {
                throw new TransferFailed($data->message);
            }

        } else {
            throw new InvalidBankDetails('Appropriate bank codes, bank currency and bank account number required for flutterwave bank transfers.');
        }
    }

    protected function getSecretKey(){
        return $this->getGatewayPrivateKey($this->gateway_type);
    }

}
    