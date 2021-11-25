<?php
namespace App\Actions\ShoppingCart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class AddShoppingCartItem extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'product_id' => 'required|integer'
      ]);
   }
   public function execute(){
      try{
         //
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   