<?php
namespace App\Actions\ProductWishList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductWish;
use App\Traits\HasAuthStatus;

class GetProductWishList extends Action{
   use HasAuthStatus;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function getWishList($auth_type){
      return ProductWish::with([
         'product:id,product_name,regular_price,sales_price,product_image',
         'variation:id,variation_name,variation_image,regular_price,sales_price'
      ])
      ->where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)
      ->orderBy('id','desc')
      ->paginate(15,['id','product_id','variation_id','product_type']);
   }

   public function execute(){
      try{
         $auth_type = $this->getUserAuthTypeObject();
         if(!isset($auth_type)) return $this->validationError("An Error occurred, please ensure that your browser allows cookies for this app.");
         $data = $this->getWishList($auth_type);
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   