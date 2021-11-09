<?php
namespace App\Actions\Product;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Product;

class GetAProduct extends Action{
   
   protected $request;
   protected $slug;
   public function __construct(Request $request,$slug){
      $this->request=$request;
      $this->slug = $slug;
   }

   protected function getProductBySlug(){
      return Product::with(['images','variations','category','brand'])
      ->where('product_slug',$this->slug)
      ->first();

   }
   public function execute(){
      try{
         return $this->successWithData($this->getProductBySlug());
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   