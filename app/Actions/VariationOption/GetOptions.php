<?php
namespace App\Actions\VariationOption;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\VariationOption;

class GetOptions extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   public function execute(){
      try{
         $data = VariationOption::all();
         return $this->successWithData($data);
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   