<?php

namespace App\Services\OrderServices;

use App\Models\SubOrder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTransaction;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShoppingCartItem;
use App\Services\OrderServices\Utilities\CreateOrderResult;
use App\Traits\HasPayment;
use App\Traits\HasProduct;
use App\Traits\HasShipping;
use App\Traits\TokenGenerator;
use App\Services\WalletServices\StoreWallet;
use App\Services\WalletServices\SuperAdminWallet as SuperAdminWalletService;
use App\Services\WalletServices\Utilities\LockDetails;
use App\Services\WalletServices\Utilities\SenderObject;
use App\Services\WalletServices\Utilities\TransactionDetails;
use Illuminate\Support\Facades\DB;

class CreateOrder
{
    use HasShipping,TokenGenerator,HasProduct,HasPayment;
    protected $deposit_and_locks = [];
    protected $processed_items = [];
    protected $user;
    protected $user_type;
    protected $order;

    public function __construct($user,$user_type){
        $this->user = $user;
        $this->user_type = $user_type;
    }

    public function onCreateOrder($transaction) : CreateOrderResult
    {
       DB::transaction(function () use($transaction) {
           $order = $this->initializeOrder($transaction);
           $this->createWalletFundsAndSubOrders($order,$transaction);
           $this->updateStocksAvailability();
           $this->clearShoppingCart();
           $this->order = $order;
       });
       //TODO: implement order created email
       return new CreateOrderResult($this->order,$transaction,$this->deposit_and_locks);
       
    }

    protected function updateStocksAvailability(){
        if(count($this->processed_items) > 0){
            foreach($this->processed_items as $item){
                if($item->item_type == Product::simple_product_type){
                    $product = Product::find($item->item_id);
                    if(isset($product)){$product->update([
                            'amount_in_stock' => $product->amount_in_stock - $item->quantity
                        ]);
                    }
                } else if($item->item_type == Product::variation_product_type && $item->variant_id != null){
                    ProductVariation::where('product_id',$item->item_id)
                    ->where('id',$item->variant_id)->decrement(
                        'amount_in_stock',$item->quantity
                    );
                }
            }
        }
    }

    protected function clearShoppingCart(){
        ShoppingCartItem::where('user_id',$this->user->id)
        ->where('user_type',$this->user_type)
        ->delete();
    }

    protected function initializeOrder($transaction)
    {
        $current_billing = $this->getCurrentBillingAddress($this->user->id);
        $order = Order::create([
            'billing_address_id' => $current_billing->id ?? null,
            'user_id' => $this->user->id,
            'order_number' => $this->createNumberToken(18),
            'total_amount' => $transaction->amount,
            'status' => Order::STATUS_AWAITING_SHIPPING,
            'transaction_id' => $transaction->id
        ]);
        return $order;
    }

    protected function createWalletFundsAndSubOrders($order, $transaction)
    {
        $attributes = $transaction->attributes;
        foreach ($attributes as $attribute) {
            $sub_order = $this->createSubOrder($order, $attribute, $transaction);
            $total_commission = 0;
            $store_wallet_deposit = 0;
            foreach ($attribute->items as $item) {
                $commission = $this->getProductComissionFee($item->item_id);
                $comm_obj = $this->getCommissionAndRemainderAmount($item->total_amount, $commission);
                $total_commission += $comm_obj->commission_amount;
                $store_wallet_deposit += $comm_obj->remainder;
                $this->createOrderItem($order, $sub_order, $item);
                array_push($this->processed_items,$item);
            }
            $deposit_details = $this->creditCommissionAndStoreWallets(
                $order,$sub_order,$store_wallet_deposit,$total_commission,$transaction
            );
            array_push($this->deposit_and_locks,$deposit_details);
        }
    }

    protected function creditCommissionAndStoreWallets($order, $sub_order, $store_deposit, $commission, $transaction = null)
    {
        $store_wallet = new StoreWallet($sub_order->store_id);
        $super_admin_wallet = new SuperAdminWalletService();
        $sender_obj = new SenderObject($sub_order->user_id, User::class);
        $lock_details = new LockDetails($sub_order->store_id, $order->id, $sub_order->id);
        $trx_details = null;
        if (isset($transaction)) {
            $trx_details = new TransactionDetails($transaction, OrderTransaction::class);
        }
        $deposit_details = $store_wallet->depositLockedOrderFund(
            $store_deposit,$sender_obj,$lock_details,$trx_details
        );
        $super_admin_wallet->depositLockedOrderFund(
            $commission,$sender_obj,$lock_details,$trx_details
        );
        return $deposit_details;
    }

    protected function createOrderItem($order, $sub_order, $item)
    {
        return OrderItem::create([
            'user_id' => $this->user->id,
            'sub_order_id' => $sub_order->id,
            'order_id' => $order->id,
            'product_id' => $item->item_id,
            'variation_id' => $item->variant_id,
            'product_type' => $item->item_type,
            'quantity' => $item->quantity,
            'paid_amount' => $item->total_amount,
        ]);
    }

    protected function createSubOrder($order, $attribute, $transaction)
    {
        return SubOrder::create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'store_id' => $attribute->store_id,
            'delivery_date' => now()->addDays($attribute->delivery_duration),
            'status' => Order::STATUS_AWAITING_SHIPPING,
            'amount' => $attribute->cart_amount,
            'shipping_fee' => $attribute->shipping_fee,
            'payment_status' => ($transaction->status == OrderTransaction::STATUS_VERIFIED) ?
                SubOrder::PAYMENT_STATUS_PAID : SubOrder::PAYMENT_STATUS_NOT_PAID
        ]);
    }
}
