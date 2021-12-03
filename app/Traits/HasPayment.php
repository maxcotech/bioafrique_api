<?php
namespace App\Traits;

use App\Models\OrderTransaction;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShoppingCartItem;

trait HasPayment{
    use HasAuthStatus;
    public $gateways = [
        OrderTransaction::FLUTTERWAVE => "Flutterwave",
        OrderTransaction::PAYSTACK => "Paystack"
    ];
    public $payment_status_list = [
        OrderTransaction::STATUS_COMPLETED => "Completed",
        OrderTransaction::STATUS_PENDING => "Pending",
        OrderTransaction::STATUS_CANCELLED => "Cancelled"
    ];

    public function getGatewayPublicKey($gateway){
        switch($gateway){
            case OrderTransaction::FLUTTERWAVE: return env('FLUTTERWAVE_PK');
            case OrderTransaction::PAYSTACK: return env('PAYSTACK_PK');
            default: return null;
        }
    }

    public function paymentGatewayExists($gateway){
        $out = $this->getGatewayPublicKey($gateway);
        if($out === null){
            return false;
        }
        return true;
    }

    public function getCartItemPrice($cart_item){
        $cart_item = json_decode(json_encode($cart_item));
        $price = 0;
        if(!isset($variation_id) && $cart_item->item_type != Product::variation_product_type){
            $product = Product::find($cart_item->item_id);
            if($product->sales_price != null && $product->sales_price != 0){
                $price = $product->sales_price;
            } else { $price = $product->regular_price; }
        } else {
            $variation = ProductVariation::where('product_id',$cart_item->item_id)
            ->where('id',$cart_item->variant_id)->first();
            if(isset($variation)){
                if($variation->sales_price != null && $variation->sales_price != 0){
                    $price = $variation->sales_price;
                } else {
                    $price = $variation->sales_price;
                }
            } else {
                $price = 0;
            }
        }
        return $price * $cart_item->quantity;
    }

    public function appendCartItemsPriceToList($cart_items,$priceKey = "item_price"){
        if(!is_object($cart_items) && !is_array($cart_items)) return [];
        $data = [];
        $cart_items = json_decode(json_encode($cart_items),true);
        foreach($cart_items as $cart_item){
            $cart_item[$priceKey] = $this->getCartItemPrice($cart_item);
            array_push($data,$cart_item);
        }
        return $data;
    }

    public function getCartTotalPrice($cart_items){
        $total = 0;
        $cart_items = json_decode(json_encode($cart_items),true);
        foreach($cart_items as $cart_item){
            $total += $this->getCartItemPrice($cart_item);
        }
        return $total;
    }

    public function getGatewayPrivateKey($gateway){
        switch($gateway){
            case OrderTransaction::FLUTTERWAVE: return env('FLUTTERWAVE_SK');
            case OrderTransaction::PAYSTACK: return env('PAYSTACK_SK');
            default: return null;
        }
    }

    public function sumPriceInTwoRelatedArraysByKeys($array1,$array2,$price_key1,$price_key2,$idKey1,$idKey2){
        $array1 = json_decode(json_encode($array1),true);
        $array2 = json_decode(json_encode($array2),true);
        $output_array = [];
        if(!isset($array1) || count($array1) == 0 || !isset($array2) || count($array2) == 0) return [];
        if(count($array1) != count($array2)) throw new \Exception('Lists to sum does not match');
        foreach($array1 as $one_item){
            foreach($array2 as $two_item){
                if($one_item[$idKey1] == $two_item[$idKey2]){
                    array_push($output_array,[
                        'relation_key' => $one_item[$idKey1],
                        'summation_value' => $one_item[$price_key1] + $two_item[$price_key2]
                    ]);
                }
            }
        }
        return $output_array;
    }




}
    