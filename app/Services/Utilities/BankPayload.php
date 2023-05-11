<?php 
namespace App\Services\Utilities;


class BankPayload {
    public $bank_code;
    public $account_number;
    public $account_name;
    public $sort_code;
    public $bank_currency_code;
    public function __construct($bank_code = null,$account_number = null,$currency_code = null, $account_name = null, $sort_code = null){
        $this->bank_code = $bank_code;
        $this->account_number = $account_number;
        $this->bank_currency_code = $currency_code;
        $this->account_name = $account_name;
        $this->sort_code = $sort_code;
    }
}