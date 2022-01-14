<?php
namespace App\Actions\ProductReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductReview;
use App\Traits\HasProductReview;
use App\Traits\HasResourceStatus;

class GetReviews extends Action{
   use HasProductReview,HasResourceStatus;
   protected $request;
   protected $review_id;

   public function __construct(Request $request,$review_id = null){
      $this->request=$request;
      $this->review_id = $review_id;
   }

   protected function validate(){
      $data = $this->request->all();
      $data['review_id'] = $this->review_id;
      $val = Validator::make($data,[
         'product_type' => 'required|string',
         'review_id' => 'nullable|integer|exists:product_reviews,id',
         'product_id' => 'required_if:review_id,null|integer|exists:products,id',
         'variation_id' => 'nullable|integer',
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getProductReviewQuery(){
      $query = ProductReview::where('product_id',$this->request->query('product_id'))
      ->where('variation_id',$this->request->query('variation_id'))
      ->where('product_type',$this->request->query('product_type'))
      ->where('status',$this->getResourceActiveId());
      return $query;
   }

   protected function onGetReview(){
      if($this->review_id != null){
         return ProductReview::find($this->review_id);
      } else {
         $query = $this->getProductReviewQuery();
         $result = $query->paginate($this->request->query('limit',30));
         return $this->appendSummary($result);
      }
   }

   protected function appendSummary($data){
      $query = $this->getProductReviewQuery();
      $all_reviews = $query->get();
      $collection = collect([
         'review_average'=>$this->getReviewAverage($all_reviews,"star_rating"),
         'review_summary'=>$this->getReviewSummary($all_reviews,"star_rating")
      ]);
      return $collection->merge($data);
   }

   public function execute(){
     try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->onGetReview();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   