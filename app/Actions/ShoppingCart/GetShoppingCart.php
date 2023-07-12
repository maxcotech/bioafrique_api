<?php

namespace App\Actions\ShoppingCart;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Http\Resources\CurrencyResource;
use App\Models\ShoppingCartItem;
use App\Traits\HasAuthStatus;
use App\Traits\HasProduct;
use App\Traits\HasRateConversion;
use App\Traits\HasShoppingCartItem;
use Illuminate\Support\Facades\Auth;

class GetShoppingCart extends Action
{
   use HasAuthStatus, HasShoppingCartItem, HasProduct, HasRateConversion;
   protected $request;
   public function __construct(Request $request)
   {
      $this->request = $request;
   }

   protected function onGetShoppingCart($auth_type)
   {
      return ShoppingCartItem::with([
         'variation:id,variation_image,variation_name,regular_price,sales_price,amount_in_stock',
         'product:id,product_name,product_image,regular_price,sales_price,amount_in_stock',
         'store:id,store_name,store_slug'
      ])
         ->where('user_id', $auth_type->id)
         ->where('user_type', $auth_type->type)
         ->paginate(15, ['id', 'item_id', 'variant_id', 'item_type', 'quantity', 'store_id']);
   }


   public function execute()
   {
      try {
         $auth_type_obj = $this->getUserAuthTypeObject();
         if (!isset($auth_type_obj)) throw new \Exception('An Error occurred, please ensure your browser enables usage of cookies.');
         $data = $this->onGetShoppingCart($auth_type_obj);
         $data = $this->appendWishListStatus($data, $auth_type_obj, "item_id");
         $cookie = $this->getUserByCookie();
         $user = Auth::user();
         $currency = $this->getUserCurrency($user, $cookie);
         $collect = collect([
            'currency' => isset($currency) ? new CurrencyResource($currency) : null,
            'cart_count' => $this->getTotalCartCount($auth_type_obj)
         ]);
         $data = $collect->merge($data);
         return $this->successWithData($data);
      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
}
