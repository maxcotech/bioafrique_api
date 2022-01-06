<?php 
namespace App\Services\WalletServices\Utilities;

class TransferRecipient{
    public $recipient_id;
    public $recipient_type;

    public function __construct($recipient_id,$recipient_type)
    {
        $this->recipient_id = $recipient_id;
        $this->recipient_type = $recipient_type;
    }
}