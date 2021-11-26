<?php
namespace App\Actions\ShoppingCart;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShoppingCartItem;
use App\Traits\HasAuthStatus;

class GetShoppingCart extends Action{
   use HasAuthStatus;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function onGetShoppingCart($auth_type){
      return ShoppingCartItem::with([
         'variation:id,variation_image,variation_name',
         'product:id,product_name,product_image,regular_price,sales_price',
         'store:id,store_name,store_slug'
      ])
      ->where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)
      ->paginate(15,['id','item_id','variant_id','item_type','quantity','store_id']);
   }


   public function execute(){
      try{
         $auth_type_obj = $this->getUserAuthTypeObject();
         if(!isset($auth_type_obj)) throw new \Exception('An Error occurred, please ensure your browser enables usage of cookies.');
         $data = $this->onGetShoppingCart($auth_type_obj);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   