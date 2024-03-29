<?php
namespace App\Actions\ShoppingCart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ShoppingCartItem;
use App\Traits\HasAuthStatus;
use App\Traits\HasResourceStatus;
use App\Traits\HasShoppingCartItem;
use Illuminate\Validation\Rule;

class AddShoppingCartItem extends Action{
   use HasAuthStatus,HasShoppingCartItem,HasResourceStatus;
   protected $request;
   protected const default_quantity = 1;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'item_id' => ['required','integer',Rule::exists('products','id')->where(function($query){
            return $query->where('product_status',$this->getResourceActiveId());
         })],
         'variant_id' => ['nullable','integer',Rule::exists('product_variations','id')
         ->where(function($query){
            return $query->where('product_id',$this->request->item_id);
         })]
      ]);
      return $this->valResult($val);
   }


   protected function addItemToCart(object $auth_type,$store_id){
      ShoppingCartItem::create([
         'user_id' => $auth_type->id,
         'user_type' => $auth_type->type,
         'item_id' => $this->request->item_id,
         'variant_id' => $this->request->input('variant_id',null),
         'quantity' => self::default_quantity,
         'store_id' => $store_id,
         'item_type' => ($this->request->input('variant_id',null) != null)? Product::variation_product_type: Product::simple_product_type
      ]);
   }

   protected function variationIsRequired($product_type){
      if($product_type == Product::variation_product_type){
         if($this->request->input('variant_id',null) == null){
            return true;
         }
         return false;
      }
      return false;
   }
  

   
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $auth_type_obj = $this->getUserAuthTypeObject();
         if(isset($auth_type_obj)){
            $product = Product::find($this->request->item_id);
            if(!isset($product)) return $this->validationError('Invalid product selected.');
            $variant_id = $this->request->input('variant_id',null);
            if($this->productAlreadyInCart($auth_type_obj,$product->id,$variant_id)) return $this->validationError("This Product is already added to cart.");
            if($this->variationIsRequired($product->product_type)) return $this->validationError('Please select a variation.');
            if($this->quantityIsBeyondAvailable($product,$this->request->input('variant_id',null),self::default_quantity)){
               return $this->validationError("Sorry, ".$product->product_name." is currently out of stock.");
            }
            $this->addItemToCart($auth_type_obj,$product->store_id);
            return $this->successMessage($product->product_name." successfully added to cart.");
         } else {
            throw new \Exception('An error occurred, please make sure that your browser allows cookies on this app.');
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   