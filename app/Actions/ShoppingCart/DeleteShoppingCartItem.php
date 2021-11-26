<?php
namespace App\Actions\ShoppingCart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ShoppingCartItem;
use App\Traits\HasAuthStatus;

class DeleteShoppingCartItem extends Action{
   use HasAuthStatus;
   protected $request;
   protected $cart_row_id;
   public function __construct(Request $request,$id){
      $this->request=$request;
      $this->cart_row_id = $id;
   }

   protected function validate(){
      $val = Validator::make(['selected_item' => $this->cart_row_id],[
         'selected_item' => 'required|integer|exists:shopping_cart_items,id'
      ]);
      return $this->valResult($val);
   }

   protected function onDeleteCartItem(object $auth_type){
      ShoppingCartItem::where('id',$this->cart_row_id)
      ->where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type)
      ->delete();
   }


   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $auth_type = $this->getUserAuthTypeObject();
         $this->onDeleteCartItem($auth_type);
         return $this->successMessage('Item successfully removed from cart.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   