<?php
namespace App\Actions\ProductReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductReview;

class CreateReview extends Action{
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request = $request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'product_id'=>'required|integer|exists:products,id',
         'variation_id'=>'nullable|integer|exists:product_variations,id',
         'star_rating' => 'required|integer',
         'comment' => 'required|string'
      ]);
      return $this->valResult($val);
   }

   protected function createReview(){
      //TODO: to be implemented
   }

   protected function reviewAlreadyExists(){
      return ProductReview::where('product_id',$this->request->product_id)
      ->where('variation_id',$this->request->input('variation_id',null))
      ->where('user_id',$this->user->id)
      ->exists();
   }
   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if($this->reviewAlreadyExists()) return $this->validationError('You have previously added a review for this product.');

      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   