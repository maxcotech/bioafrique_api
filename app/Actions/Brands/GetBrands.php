<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;
use App\Traits\HasResourceStatus;

class GetBrands extends Action{
   use HasResourceStatus;

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function hasQuery(){
      $query = $this->request->query('query',null);
      if($query != null && trim($query) != ""){
         return true;
      }
      return false;
   }


   public function execute(){
      try{
         $query = Brand::where('status',$this->getResourceActiveId());
         $data = $query->paginate();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   