<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class DeleteProduct extends Action{
   use HasRoles;

   protected $request,$product_id,$user;
   public function __construct(Request $request,$product_id){
      $this->request=$request;
      $this->user = $request->user();
      $this->product_id = $product_id;
   }

   protected function validate(){
      $val = Validator::make(['product_id'=>$this->product_id],[
         'product_id' => 'required|integer|exists:products,id'
      ]);
      return $this->valResult($val);
   }

   protected function userIsEligible($product){
      if($this->isSuperAdmin($this->user->user_type)){
         return true;
      } else if($this->isStoreOwner($this->user->user_type)){
         if($this->user->store->id == $product->store_id){
            return true;
         }
      } else if($this->isStoreStaff($this->user->user_type)){
         $stores = $this->user->workStores;
         if(isset($stores) && count($stores) > 0){
            foreach($stores as $store){
               if($store->id == $product->store_id){
                  return true;
               }
            }
         }
      }
      return false;
   }

   protected function deleteProductAndAttributes($product){
      DB::transaction(function () use($product) {
         $variations = $product->variations;
         if(isset($variations) && count($variations) > 0){
            foreach($variations as $variation){
               $variation->options()->delete();
               $variation->delete();
            }
         }
         $product->delete();
      });
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] !== "success") return $this->resp($val);
         $product = Product::where('id',$this->product_id)->first();
         if($this->userIsEligible($product)){
            $this->deleteProductAndAttributes($product);
            return $this->successMessage('Product was deleted successfully.');
         }
         return $this->notAuthorized('You are not authorized to carry out this operation.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   