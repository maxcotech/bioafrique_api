<?php
namespace App\Actions\ProductWishList;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ProductWish;
use App\Traits\HasArrayOperations;
use App\Traits\HasAuthStatus;
use App\Traits\HasProductFilters;
use App\Traits\HasProductReview;
use App\Traits\HasShoppingCartItem;

class GetProductWishList extends Action{
   use HasAuthStatus, HasArrayOperations,HasProductFilters,HasShoppingCartItem,HasProductReview;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function getWishList($auth_type){
      return ProductWish::where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)
      ->orderBy('id','desc')
      ->paginate(15,['id','product_id','variation_id','product_type']);
   }


   protected function getProductsInWishList($list,$auth_type){
      $product_ids = [];
      $list->each(function($item)use(&$product_ids){
         if(!in_array($item->product_id,$product_ids)){
            array_push($product_ids,$item->product_id);
         }
      });
      $query = Product::whereIn('id',$product_ids);
      $query = $query->select('id','product_name','product_image','regular_price','sales_price','product_slug','store_id','product_type','product_status','amount_in_stock');
      $query = $this->selectFields($query);
      $products = $query->get();
      $products = $this->appendReviewAverage($products);
      $products = $this->appendCartQuantityToEachItem($products,$auth_type);
      return $products;
   }

   protected function appendProductsToList($list,$auth_type){
      if(!isset($list) || count($list) < 1) return $list;
      $products = $this->getProductsInWishList($list,$auth_type);
      $list->each(function($item)use($products){
         $item->product = $this->selectArrayItemByKeyPair('id',$item->product_id,$products);
      });
      return $list;
   }

   public function execute(){
      try{
         $auth_type = $this->getUserAuthTypeObject();
         if(!isset($auth_type)) return $this->validationError("An Error occurred, please ensure that your browser allows cookies for this app.");
         $data = $this->getWishList($auth_type);
         $data = $this->appendProductsToList($data,$auth_type);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   