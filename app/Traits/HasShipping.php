<?php
namespace App\Traits;

use App\Models\BillingAddress;
use App\Models\City;
use App\Models\Product;
use App\Models\ShippingGroup;
use App\Models\ShippingLocation;
use App\Models\State;
use App\Models\User;

trait HasShipping{
   use HasShoppingCartItem,HasArrayOperations,HasRateConversion;
   protected function getStateModelByName(){
      $state = null;
      if(request()->input('state',null) != null && request()->input('country_id') != null){
         $state = State::where('state_name',request()->input('state'))
         ->where('country_id',request()->input('country_id'))->first();
      }
      return $state;
   }

   protected function getCityModelByName($state_id){
      $city = null;
      if(request()->input('city',null) != null && isset($state_id)){
         $city = City::where('city_name',request()->input('city'))
         ->where('state_id',$state_id)->first();
      }
      return $city;
   }

   protected function collateShippingDetailsByLocation($user,$convert_to_base_rates = true){
      if(!isset($user)) throw new \Exception('You need to login inorder to continue.');
      $cart_items = $this->getShoppingCartItems($user->id,User::auth_type);
      if(!isset($cart_items) || count($cart_items) == 0) throw new \Exception('Could not find any cart item.');
      $address = $this->getCurrentBillingAddress($user->id);
      $store_ids = $this->extractUniqueValueList($cart_items,"store_id");
      $shipping_groups = $this->getShippingGroups($store_ids,$address);
      $shipping_fee_data = [];
      $shipping_fee_data['group_shipping_details'] = $this->getShippingDetailsForEachShippingGroup($cart_items,$shipping_groups,$convert_to_base_rates);
      $shipping_fee_data['grand_total_shipping_fees'] = $this->sumArrayValuesByKey($shipping_fee_data['group_shipping_details'],'total_shipping_fee');
      return $shipping_fee_data;
   }

   protected function getShippingDetailsForEachShippingGroup($cart_items,$unique_groups,$convert_to_base_rates = true){
      $cart_items = json_decode(json_encode($cart_items));
      $groups = json_decode(json_encode($unique_groups));
      $output = []; $count = 1; $total_shipping = count($unique_groups);
      foreach($groups as $group){
         $total_shipping_fee = 0;
         $items = [];
         $total_dimensions = 0;
         foreach($cart_items as $cart_item){
            if($cart_item->store_id == $group->store_id){
               $product = Product::find($cart_item->item_id);
               $total_dimensions += $this->getShippingDimension($product,$cart_item->quantity);
               array_push($items,[
                  'item_id' => $cart_item->item_id, 'variant_id' => $cart_item->variant_id,
                  'item_type' => $cart_item->item_type,'item_name' => $product->product_name
               ]);
            }
         }
         $total_shipping_fee = $this->getNonDimensionalShippingFees($group);
         $total_shipping_fee += $this->getDimensionRangeRateValue($total_dimensions,json_decode($group->dimension_range_rates));
         array_push($output,[
            'shipping_label' => "Shipping ".$count." of ".$total_shipping,'store_id' => $group->store_id,
            'total_shipping_fee' => ($convert_to_base_rates == true)? $this->userToBaseCurrency($total_shipping_fee):$total_shipping_fee,
            'delivery_duration' => $group->delivery_duration,
            "delivery_note" => "Item(s) will be delivered between "
            .now()->addDays($group->delivery_duration)->toFormattedDateString()
            ." and ".now()->addDays($group->delivery_duration + 3)->toFormattedDateString()
            .".\n(PLEASE NOTE: Items(s) may arrive before these dates).",'items' => $items
         ]);
         $count++;
      }
      return $output;
   }

   
   protected function getNonDimensionalShippingFees($group){
      $total = 0;
      $total += $group->shipping_rate ?? 0;
      return $total;
   }


   protected function getTotalShippingFeeForItem($product,$group){
      $total = 0;
      $dimension = $this->getShippingDimension($product);
      $dim_range_value = $this->getDimensionRangeRateValue($dimension,json_decode($group->dimension_range_rates));
      $total += $group->shipping_rate ?? 0;
      $total += $dim_range_value;
      return $total;
   }

   protected function getShippingDimension($product,$quantity = 1){
      $dimension = 0;
      $dim_divisor = 3500; //converts dimensions to kilogram.
      if($product->dimension_width != null && $product->dimension_height != null && $product->dimension_length != null){
         $dimensions = $product->dimension_width * $product->dimension_height * $product->dimension_length;
         $dimension = round($dimensions / $dim_divisor);
         if($product->weight != null && $product->weight > $dimension) $dimension = $product->weight;
      } else {
         $dimension = $product->weight;
      }
      return $dimension * $quantity;
   }

   protected function getDimensionRangeRateValue($dimension,$range_rates,$max_key = "max",$min_key = "min",$rate_key = "rate"){
      if(!isset($range_rates) || !isset($dimension)) return 0;
      $ranges = json_decode(json_encode($range_rates),true);
      if(count($ranges) == 0) return 0;
      $range_value = 0;
      foreach($ranges as $range){
         if($dimension <= $range[$max_key] && $dimension >= $range[$min_key]){
            $range_value = $range[$rate_key];
         }
      }
      return $range_value;
   }

   protected function getShippingGroups($store_ids,$user_addr){
      $groups = [];
      foreach($store_ids as $store_id){
         $group = $this->getShippingGroup($store_id,$user_addr);
         if(!isset($group)) throw new \Exception('Could not find shipping details for some of the items in the cart.');
         array_push($groups,$group);
      }
      return $groups;
   }


   protected function getShippingGroup($store_id,$user_addr){
      $store_location = ShippingLocation::where('store_id',$store_id)
      ->where('country_id',$user_addr->country_id)
      ->where('state_id',$user_addr->state_id)
      ->where('city_id',$user_addr->city_id)->first();
      if(!isset($store_location)){
         $store_location = ShippingLocation::where('store_id',$store_id)
         ->where('country_id',$user_addr->country_id)
         ->where('state_id',$user_addr->state_id)
         ->where('city_id',null)->first();
         if(!isset($store_location)){
            $store_location = ShippingLocation::where('store_id',$store_id)
            ->where('country_id',$user_addr->country_id)
            ->where('state_id',null)
            ->where('city_id',null)
            ->first();
         }
      }
      if(isset($store_location)){
         return ShippingGroup::where('id',$store_location->shipping_group_id)
         ->where('store_id',$store_id)->first();
      }
      return null;
   }



   protected function getCurrentBillingAddress($user_id){
      $with_data = ['country:id,country_name','state:id,state_name','city:id,city_name'];
      $address = BillingAddress::with($with_data)->where('user_id',$user_id)
      ->where('is_current',1)->first();
      if(!isset($address)){
         $address = BillingAddress::with($with_data)
         ->where('user_id',$user_id)->first();
         if(!isset($address)){
            throw new \Exception('You must create a billing address before you can continue.');
         }
      }
      return $address;
   }

}
   