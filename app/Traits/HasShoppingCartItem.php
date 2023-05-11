<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ShoppingCartItem;

trait HasShoppingCartItem
{

   use HasArrayOperations;
   protected function quantityIsBeyondAvailable(Product $product, int $variant_id = null, int $input_quantity)
   {
      $amount_in_stock = 0;
      if (isset($variant_id)) {
         $variant = $product->variations()->where('id', $variant_id)->first();
         if (isset($variant)) {
            $amount_in_stock = $variant->amount_in_stock;
         } else {
            $amount_in_stock = $product->amount_in_stock;
         }
      } else {
         $amount_in_stock = $product->amount_in_stock;
      }
      if ($input_quantity > $amount_in_stock) {
         return true;
      } else {
         return false;
      }
   }

   protected function productAlreadyInCart(object $auth_type, int $item_id, int $variant_id = null)
   {
      return ShoppingCartItem::where('user_id', $auth_type->id)
         ->where('user_type', $auth_type->type)
         ->where('item_id', $item_id)
         ->where('variant_id', $variant_id)
         ->exists();
   }

   protected function getShoppingCartItems($user_id, $user_type)
   {
      return ShoppingCartItem::where('user_id', $user_id)
         ->where('user_type', $user_type)
         ->get();
   }

   protected function getTotalCartCount($auth_type)
   {
      $cart_items = ShoppingCartItem::where('user_id', $auth_type->id)
         ->where('user_type', $auth_type->type)->get();
      if (count($cart_items) > 0) {
         $count = 0;
         foreach ($cart_items as $item) {
            $count += 1 * $item->quantity;
         }
         return $count;
      }
      return 0;
   }

   protected function appendCartQuantityToEachItem($items, $auth_type)
   {
      $cart_items = ShoppingCartItem::where('user_id', $auth_type->id)
         ->where('user_type', $auth_type->type)->select('id', 'item_id', 'variant_id', 'quantity')->get();
      if (count($items) > 0) {
         $items->each(function ($item) use ($cart_items) {
            $item->cart_quantity = $this->sumArrayValuesByKey($cart_items, "quantity", "item_id", $item->id);
            if ($item->product_type == Product::variation_product_type || count($item->variations) > 0) {
               if (count($item->variations) > 0) {
                  $item->variations->each(function ($variation) use ($cart_items) {
                     $variation->cart_quantity = $this->sumArrayValuesByKey($cart_items, 'quantity', 'variant_id', $variation->id);
                  });
               }
            }
         });
      }
      return $items;
   }

   protected function appendCartQuantityToProduct($item, $auth_type)
   {
      $cart_items = ShoppingCartItem::where('user_id', $auth_type->id)
         ->where('user_type', $auth_type->type)->select('id', 'item_id', 'variant_id', 'quantity')->get();
      $item->cart_quantity = $this->sumArrayValuesByKey($cart_items, "quantity", "item_id", $item->id);
      if ($item->product_type == Product::variation_product_type || count($item->variations) > 0) {
         if (count($item->variations) > 0) {
            $item->variations->each(function ($variation) use ($cart_items) {
               $variation->cart_quantity = $this->sumArrayValuesByKey($cart_items, 'quantity', 'variant_id', $variation->id);
            });
         }
      }
      return $item;
   }
}
