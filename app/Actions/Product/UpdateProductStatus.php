<?php
namespace App\Actions\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductStatus extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      $val = Validator::make($this->request->all(),[
         'status' => 'required|integer',
         'id' => 'required|integer|exists:products,id'
      ]);
      return $this->valResult($val);
   }

   protected function updateProductStatus(){
      $product = Product::find($this->request->id);
      if(isset($product)){
         DB::transaction(function()use($product){
            $product->variations()->update([
               'variation_status' => $this->request->status
            ]);
            $product->update(['product_status' => $this->request->status]);
         });
      }
   }

   public function execute(){
      try{
         $val = $this->validate();
         if($val['status'] != "success") return $this->resp($val);
         $this->updateProductStatus();
         return $this->successMessage('Product Status Updated Successfully.');
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   