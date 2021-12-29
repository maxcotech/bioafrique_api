<?php
namespace App\Actions\ProductWishList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ProductWish;
use App\Traits\HasAuthStatus;
use App\Traits\HasResourceStatus;
use Illuminate\Validation\Rule;

class AddWishProduct extends Action{
   use HasAuthStatus,HasResourceStatus;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'product_id' => ['required','integer',Rule::exists('products','id')->where(function($query){
            return $query->where('product_status',$this->getResourceActiveId());
         })],
         'variation_id' => ['nullable','integer',Rule::exists('product_variations','id')
         ->where(function($query){
            return $query->where('product_id',$this->request->product_id);
         })]
      ]);
      return $this->valResult($val);
   }

   protected function productIsAlreadyInList($auth_type){
      return ProductWish::where('product_id',$this->request->product_id)
      ->where('variation_id',$this->request->input('variation_id',null))
      ->where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)
      ->exists();
   }

   protected function onAddToWishList($auth_type,$product_type){
      ProductWish::create([
         'product_id' => $this->request->product_id,
         'variation_id' => $this->request->input('variation_id',null),
         'user_id' => $auth_type->id,
         'user_type' => $auth_type->type,
         'product_type' => $product_type
      ]);
   }

   protected function variationIsRequired($product_type){
      if($product_type == Product::variation_product_type){
         if($this->request->input('variation_id',null) == null){
            return true;
         }
         return false;
      }
      return false;
   }
   
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $auth_type = $this->getUserAuthTypeObject();
         $product = Product::find($this->request->product_id);
         if(!isset($auth_type)) return $this->validationError('An Error Occurred, please ensure your browser allows cookies on this app.');
         if($this->productIsAlreadyInList($auth_type)) return $this->validationError('Product already added to wish list.');
         //if($this->variationIsRequired($product->product_type)) return $this->validationError('Please select a variation.');
         $this->onAddToWishList($auth_type,$product->product_type);
         return $this->successMessage('Product added to wish list.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   