<?php
namespace App\Actions\State;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class CreateState extends Action{
   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }

   protected function validate(){
      
   }
   public function execute(){
      try{
         //
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }

}
   