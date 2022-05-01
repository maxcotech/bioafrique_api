<?php 
namespace App\Services\OrderServices\Utilities;

use App\Models\BillingAddress;
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

    protected function getOrderBillingAddress($id){
        $query = BillingAddress::where('id',$id);
        $query = $query->with([
            'state:id,state_name','city:id,city_name','country:id,country_name'
        ]);
        $query = $query->select('id','street_address','country_id','state_id','city_id','phone_number',
        'telephone_code','postal_code','first_name','last_name');
        return $query->first();
    }

    public function getResult(){
        $data = [
            'order' => $this->order,
            'transaction' => $this->transaction,
            'billing_address' => $this->getOrderBillingAddress($this->order->billing_address_id),
            'order_items' => $this->getOrderItemDetails($this->order->items),
            'deposit_and_locks' => $this->deposit_and_locks
         ];
         return $data;
    }
}