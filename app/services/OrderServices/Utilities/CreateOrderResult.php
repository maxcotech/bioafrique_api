<?php 
namespace App\Services\OrderServices\Utilities;

use App\Traits\HasProduct;

class CreateOrderResult {
    use HasProduct;

    public $order;
    public $transaction;
    public $deposit_and_locks;

    public function __construct($order,$transaction,$deposit_and_locks){
        $this->order = $order;
        $this->transaction = $transaction;
        $this->deposit_and_locks = $deposit_and_locks;
    }

    public function getResult(){
        $data = [
            'order' => $this->order,
            'order_items' => $this->getOrderItemDetails($this->order->items),
            'deposit_and_locks' => $this->deposit_and_locks
         ];
         return $data;
    }
}