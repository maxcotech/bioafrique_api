<?php
namespace App\Actions\ProductReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\OrderItem;
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
         'star_rating' => 'required|integer|min:0,max:5',
         'review_comment' => 'required|string'
      ]);
      return $this->valResult($val);
   }

   protected function createReview(){
      ProductReview::create([
         'product_id' => $this->request->product_id,
         'variation_id' => $this->request->variation_id,
         'user_id' => $this->user->id,
         'review_comment' => $this->request->review_comment,
         'star_rating' => $this->request->star_rating
      ]);
   }

   protected function reviewAlreadyExists(){
      return ProductReview::where('product_id',$this->request->product_id)
      ->where('variation_id',$this->request->input('variation_id',null))
      ->where('user_id',$this->user->id)
      ->exists();
   }

   protected function userPurchasedProduct(){
      return OrderItem::where('user_id',$this->user->id)
      ->where('product_id',$this->request->product_id)
      ->where('variation_id',$this->request->variation_id)
      ->exists();
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         if(!$this->userPurchasedProduct()) return $this->validationError('You need to purchase this product inorder to be eligible to review it.');
         if($this->reviewAlreadyExists()) return $this->validationError('You have previously added a review for this product.');
         $this->createReview();
         return $this->successMessage('Your review was posted successfully');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   