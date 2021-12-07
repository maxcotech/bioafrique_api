<?php 
namespace App\Services\WalletServices\Utilities;

class SenderObject{
    public $sender_id;
    public $sender_type;
    public function __construct(int $sender_id,string $sender_type){
        $this->sender_id = $sender_id;
        $this->sender_type = $sender_type;
    }
}