<?php
namespace App\Actions\ProductReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductReview;
use App\Traits\HasRoles;

class DeleteReview extends Action{
   use HasRoles;
   protected $request;
   protected $user;
   protected $id;
   public function __construct(Request $request,$id){
      $this->request=$request;
      $this->user = $request->user();
      $this->id = $id;
   }

   protected function validate(){
      $val = Validator::make(['review_id'=>$this->id],[
         'review_id' => 'required|integer|exists:product_reviews,id'
      ]);
      return $this->valResult($val);
   }

   protected function userIsEligible($review){
      if($this->isSuperAdmin()){
         return true;
      } else if($this->user->id == $review->user_id){
         return true;
      } else {
         return false;
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $review = ProductReview::find($this->id);
         if(!$this->userIsEligible($review)) return $this->notAuthorized("You are not authorized to carry out this operation");
         $review->delete();
         return $this->successMessage('Review was deleted successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   