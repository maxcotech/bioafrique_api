<?php
namespace App\Actions\ProductReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\ProductReview;
use App\Traits\HasArrayOperations;
use App\Traits\HasResourceStatus;

class PendingReviews extends Action{
   use HasArrayOperations,HasResourceStatus;
   protected $request;
   protected $user;
   public function __construct(Request $request){
      $this->request = $request;
      $this->user = $request->user();
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'limit' => 'nullable|integer'
      ]);
      return $this->valResult($val);
   }

   protected function getPendingReviews(){
      $data = ProductReview::with([
         'product:id,product_name,product_image,product_slug,regular_price,sales_price',
         'variation:id,variation_name,variation_image,product_id,regular_price,sales_price'
      ])
      ->where('user_id',$this->user->id)
      ->where('status',$this->getResourceInactiveId())
      ->paginate($this->request->query('limit',15));
      return $data;
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $data = $this->getPendingReviews();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   