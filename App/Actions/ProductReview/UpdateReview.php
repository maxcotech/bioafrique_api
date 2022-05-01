<?php
namespace App\Actions\ProductReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductReview;
use App\Traits\HasRoles;

class UpdateReview extends Action{
   use HasRoles;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request=$request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'id' => 'required|integer|exists:product_reviews,id',
         'star_rating' => 'required|integer|min:0,max:5',
         'review_comment' => 'required|string',
      ]);
      return $this->valResult($val);
   }

   protected function userIsEligible($review){
      if($this->isSuperAdmin($this->user->user_type)){
         return true;
      } else {
         if($this->user->id == $review->user_id){
            return true;
         }
      }
      return false;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $review = ProductReview::find($this->request->id);
         if(!$this->userIsEligible($review)) return $this->validationError('You are not authorized to carry out this operation');
         $review->update($this->request->all());
         return $this->successMessage('Your review update was posted successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   