<?php
namespace App\Actions\Product;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use App\Models\ProductWish;
use App\Traits\HasAuthStatus;
use App\Traits\HasProduct;

class GetAProduct extends Action{
   use HasAuthStatus,HasProduct;
   protected $request;
   protected $param;
   protected $access_type;
   public function __construct(Request $request,$param){
      $this->request=$request;
      $this->param = $param;
      $this->access_type = $this->getUserAuthTypeObject($request->user());
   }

   protected function getProductBySlugOrId(){
      $query = Product::with(['images','variations','category','brand','store:id,store_name']);
      if(is_numeric($this->param)){
         $query = $query->where('id',$this->param);
      } else {
         $query = $query->where('product_slug',$this->param);

      }
      return $query->first();
   }

   protected function inWishList($product_id){
      $user_id = $this->access_type->id;
      $user_type = $this->access_type->type;
      $result = ProductWish::where('user_id',$user_id)->where('user_type',$user_type)
      ->pluck('product_id');
      $product_ids = json_decode(json_encode($result),true);
      return in_array($product_id,$product_ids);
   }

   public function execute(){
      try{
         $data = $this->getProductBySlugOrId();
         if(isset($data)){
            $data->in_wishlist = $this->inWishList($data->id);
            $data->append('review_summary');
            $this->addToRecentlyViewed($data->id,$this->access_type);
         }
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   