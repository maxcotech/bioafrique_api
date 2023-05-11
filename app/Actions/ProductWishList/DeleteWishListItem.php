<?php
namespace App\Actions\ProductWishList;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductWish;
use App\Traits\HasAuthStatus;
use App\Traits\Message;
use Illuminate\Support\Facades\Validator;

class DeleteWishListItem extends Action{
   use HasAuthStatus,Message;
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'nullable|integer|exists:product_wishes,id',
         'product_id' => 'required_if:id,null|integer',
         'variation_id' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }


   protected function onDeleteItem($auth_type){
      $query = ProductWish::where('user_id',$auth_type->id)
      ->where('user_type',$auth_type->type);
      if($this->request->query('id',null) == null){
         $query->where('product_id',$this->request->query('product_id'))
         ->where('variation_id',$this->request->query('variation_id'));
      } else {
         $query->where('id',$this->request->id);
      }
      return $query->delete();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $auth_type = $this->getUserAuthTypeObject();
         if(!isset($auth_type)) return $this->validationError($this->getCookieErrorMessage());
         $this->onDeleteItem($auth_type);
         return $this->successMessage('wish list item removed successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   