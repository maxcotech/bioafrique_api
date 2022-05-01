<?php 
namespace App\Services\WalletServices\Utilities;

class LockDetails {
    public $store_id;
    public $order_id;
    public $sub_order_id;
    public function __construct($store_id,$order_id,$sub_order_id){
        $this->store_id = $store_id;
        $this->order_id = $order_id;
        $this->sub_order_id = $sub_order_id;
    }
}