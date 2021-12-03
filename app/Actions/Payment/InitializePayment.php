<?php
namespace App\Actions\Payment;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\OrderTransaction;
use App\Models\OrderTransactionAttribute;
use App\Models\OrderTransactionAttributeItem;
use App\Models\User;
use App\Traits\HasArrayOperations;
use App\Traits\HasPayment;
use App\Traits\HasShipping;
use App\Traits\TokenGenerator;
use Illuminate\Support\Facades\DB;

class InitializePayment extends Action{
   use HasPayment,TokenGenerator,HasShipping,HasArrayOperations;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function createTransactionRef(){
      $token = "";
      while(true){
         $pre_token = $this->createNumberToken(15);
         $token = "ref-".$pre_token;
         if(!OrderTransaction::where('reference',$token)->exists()){
            break;
         }
      }
      return $token;
   }

   protected function deletePreviousPendingTransactions(){
      $transactions = OrderTransaction::where('user_id',$this->user->id)
      ->where('status',OrderTransaction::STATUS_PENDING)->get();
      if(isset($transactions)){
         foreach($transactions as $transaction){
            foreach($transaction->attributes as $attribute){
               $attribute->items()->delete();
            }
            $transaction->attributes()->delete();
            $transaction->delete();
         }
      }
   }

   protected function onInitPayment($currency_id,$grand_total,$ref,$price_list,$store_ids,$shipping_details){
      DB::transaction(function () use($currency_id,$grand_total,$ref,$price_list,$store_ids,$shipping_details) {
         $this->deletePreviousPendingTransactions();
         $order_trx = $this->initPayment($currency_id,$grand_total,$ref,$price_list);
         $this->createOrderTransactionAttributes($order_trx,$price_list,$store_ids,$shipping_details);
      });
   }

   protected function initPayment($currency_id,$grand_total,$ref){
      $order_trx = OrderTransaction::create([
         'reference' => $ref,
         'user_id' => $this->user->id,
         'status' => OrderTransaction::STATUS_PENDING,
         'payment_gateway' => null,
         'currency_id' => $currency_id,
         'amount' => $grand_total
      ]);
      return $order_trx;
   }

   protected function createOrderTransactionAttributes($order_trx,$price_list,$store_ids,$shipping_details){
      foreach($store_ids as $store_id){
         $cart_amount = $this->sumArrayValuesByKey($price_list,"item_price","store_id",$store_id);
         $shipping_fee = $this->getValueFromArrayByCondition(
            $shipping_details,"total_shipping_fee","store_id",$store_id
         ); 
         $trx_attr = OrderTransactionAttribute::create([
            'order_transaction_id' => $order_trx->id,'store_id' => $store_id,
            'cart_amount' => $cart_amount,
            'shipping_fee' => $shipping_fee
         ]);
         $this->createOrderTransactionAttrItems($trx_attr,$price_list,$store_id);
      }
   }

   protected function createOrderTransactionAttrItems($trx_attr,$items,$store_id){
      $items = json_decode(json_encode($items));
      foreach($items as $item){
         if($item->store_id == $store_id){
            OrderTransactionAttributeItem::create([
               'attribute_id' => $trx_attr->id,
               'item_id' => $item->item_id,
               'variant_id' => $item->variant_id,
               'item_type' => $item->item_type,
               'quantity' => $item->quantity,
               'total_amount' => $item->item_price
            ]);
         }
      }
   }

   protected function getInitPaymentPayload($grand_total,$ref){
      return [
         'reference' => $ref,
         'paystack_public_key' => $this->getGatewayPublicKey(OrderTransaction::PAYSTACK),
         'flutterwave_public_key' => $this->getGatewayPublicKey(OrderTransaction::FLUTTERWAVE),
         'total_payment' => $grand_total,
         'total_in_base_rate' => $this->userToBaseCurrency($grand_total,$this->user),
         'customer_details' => $this->user
      ];
   }

   public function execute(){
      try{
         $user_currency = $this->user->currency;
         $ref = $this->createTransactionRef();
         $cart_items = $this->getShoppingCartItems($this->user->id,User::auth_type);
         if(count($cart_items) == 0) return $this->validationError("You don't have any item in your shopping cart.");
         $price_key = "item_price";
         $store_ids = $this->extractUniqueValueList($cart_items,"store_id");
         $price_appended_items = $this->appendCartItemsPriceToList($cart_items,$price_key);
         $cart_prices = $this->sumArrayValuesByKey($price_appended_items,$price_key);
         $shipping = $this->collateShippingDetailsByLocation($this->user,false);
         $grand_total = $cart_prices + $shipping['grand_total_shipping_fees'];
         $this->onInitPayment(
            $user_currency->first()->id,$grand_total,
            $ref,$price_appended_items,$store_ids,$shipping['group_shipping_details']
         );
         $payload = $this->getInitPaymentPayload($grand_total,$ref);
         return $this->successWithData($payload);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   