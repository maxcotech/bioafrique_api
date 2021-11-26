<?php
namespace App\Actions\ShoppingCart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ShoppingCartItem;
use App\Traits\HasAuthStatus;
use App\Traits\HasShoppingCartItem;
use Illuminate\Validation\Rule;

class AddShoppingCartItem extends Action{
   use HasAuthStatus,HasShoppingCartItem;
   protected $request;
   protected const default_quantity = 1;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'item_id' => 'required|integer|exists:products,id',
         'variant_id' => ['nullable','integer',Rule::exists('product_variations','id')
         ->where(function($query){
            return $query->where('product_id',$this->request->item_id);
         })]
      ]);
      return $this->valResult($val);
   }


   protected function addItemToCart(object $auth_type){
      ShoppingCartItem::create([
         'user_id' => $auth_type->id,
         'user_type' => $auth_type->type,
         'item_id' => $this->request->item_id,
         'variant_id' => $this->request->input('variant_id',null),
         'quantity' => self::default_quantity,
         'item_type' => ($this->request->input('variant_id',null) != null)? Product::variation_product_type: Product::simple_product_type
      ]);
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
            if($this->quantityIsBeyondAvailable($product,$this->request->input('variant_id',null),self::default_quantity)){
               return $this->validationError("Sorry, ".$product->product_name." is currently out of stock.");
            }
            $this->addItemToCart($auth_type_obj);
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
   