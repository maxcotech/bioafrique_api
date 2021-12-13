<?php
namespace App\Actions\Product;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;

class GetAProduct extends Action{
   
   protected $request;
   protected $param;
   public function __construct(Request $request,$param){
      $this->request=$request;
      $this->param = $param;
   }

   protected function getProductBySlugOrId(){
      $query = Product::with(['images','variations','category','brand']);
      if(is_numeric($this->param)){
         $query = $query->where('id',$this->param);
      } else {
         $query = $query->where('product_slug',$this->param);

      }
      return $query->first();
   }

   public function execute(){
      try{
         $data = $this->getProductBySlugOrId();
         $data->append('review_summary');
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   