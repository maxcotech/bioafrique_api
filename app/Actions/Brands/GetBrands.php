<?php
namespace App\Actions\Brands;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Brand;

class GetBrands extends Action{

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   public function execute(){
      try{
         $data = Brand::paginate();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   