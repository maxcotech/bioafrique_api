<?php
namespace App\Actions\Cookie;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Cookie;
use App\Traits\HasAccessCookie;

class CreateCookie extends Action{
   use HasAccessCookie;

   protected $request;
   public function __construct(Request $request){
      $this->request=$request;
   }
   
   public function execute(){
      try{
         if($this->isEligibleForNewCookie()){
            $cookie = $this->saveCookie();
            $cookie_payload = $this->getCookiePayload($cookie);
            return $this->successWithData($cookie)->withCookie($cookie_payload);
         }
         else{
            return $this->successWithData([]);
         }
      }
      catch(\Exception $e){
         return $this->internalError($e->getMessage());
      }
   }


}
    