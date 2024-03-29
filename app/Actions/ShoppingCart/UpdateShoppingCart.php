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

class UpdateShoppingCart extends Action{
   use HasShoppingCartItem,HasAuthStatus;

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'item_id' => 'required|integer|exists:products,id',
         'variant_id' => ['nullable','integer',Rule::exists('product_variations','id')
         ->where(function($query){
            return $query->where('product_id',$this->request->item_id);
         })],
         'quantity' => 'required|integer|min:0,max:20'
      ]);
      return $this->valResult($val);
   }

   protected function onUpdateCart($auth_type){
      if($this->request->quantity == 0){
         ShoppingCartItem::where('user_id',$auth_type->id)
         ->where('user_type',$auth_type->type)
         ->where('item_id',$this->request->item_id)
         ->where('variant_id',$this->request->variant_id)->delete();
      } else {
         ShoppingCartItem::updateOrCreate([
            'item_id' => $this->request->item_id,
            'variant_id' => $this->request->input('variant_id',null),
            'user_id' => $auth_type->id,
            'user_type' => $auth_type->type
         ],[
            'quantity' => $this->request->quantity
         ]);
      }
   }

   
   public function execute(){
      try{
         $val = $this->validate();
         $auth_type_obj = $this->getUserAuthTypeObject();
         if($val['status'] != "success") return $this->resp($val);
         $product = Product::find($this->request->item_id);
         if(isset($product)){
            $variant_id = $this->request->input('variant_id',null);
            $quantity = $this->request->quantity;
            if(!$this->productAlreadyInCart($auth_type_obj,$product->id,$variant_id)){
               return $this->validationError('You can only update items already in the cart.');
            }
            if($this->quantityIsBeyondAvailable($product,$variant_id,$quantity)){
               return $this->validationError('The stock quantity of '.$product->product_name." is currently not enough to fill your requested quantity.");
            }
            $this->onUpdateCart($auth_type_obj);
            return $this->successMessage('Cart item was updated successfully.');

         } else {
            return $this->validationError('The product you selected is invalid.');
         }

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }



}
   