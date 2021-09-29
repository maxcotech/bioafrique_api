<?php
namespace App\Actions\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class CheckCookie extends Action{
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    public function execute(){
       try{
         return $this->successWithData(
            [
               "cookie_value" => $this->request->header("X-basic_access")
            ]
         );
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    