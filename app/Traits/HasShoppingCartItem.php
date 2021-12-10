<?php
namespace App\Traits;

use App\Models\Product;
use App\Models\ShoppingCartItem;

trait HasShoppingCartItem{

   protected function quantityIsBeyondAvailable(Product $product,int $variant_id = null,int $input_quantity){
      $amount_in_stock = 0;
      if(isset($variant_id)){
         $variant = $product->variations()->where('id',$variant_id)->first();
         if(isset($variant)){
            $amount_in_stock = $variant->amount_in_stock;
         }else{
            $amount_in_stock = $product->amount_in_stock;
         }
      } else {
         $amount_in_stock = $product->amount_in_stock;
      }
      if($input_quantity > $amount_in_stock){
         return true;
      } else {
         return false;
      }
   }

   protected function productAlreadyInCart(object $auth_type,int $item_id,int $variant_id = null){
      return ShoppingCartItem::where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)
      ->where('item_id',$item_id)
      ->where('variant_id',$variant_id)
      ->exists();
   }

   protected function getShoppingCartItems($user_id,$user_type){
      return ShoppingCartItem::where('user_id',$user_id)
      ->where('user_type',$user_type)
      ->get();
   }

   protected function getTotalCartCount($auth_type){
      $cart_items = ShoppingCartItem::where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)->get();
      if(count($cart_items) > 0){
         $count = 0;
         foreach($cart_items as $item){
            $count += 1 * $item->quantity;
         }
         return $count;
      }
      return 0;
   }
   

}
   