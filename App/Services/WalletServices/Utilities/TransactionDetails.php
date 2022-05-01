<?php 
namespace App\Services\WalletServices\Utilities;

class TransactionDetails {
    public $transaction;
    public $transaction_type;
    public function __construct($transaction,$transaction_type){
        $this->transaction = $transaction;
        $this->transaction_type = $transaction_type;
    }
}