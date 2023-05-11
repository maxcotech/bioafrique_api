<?php
namespace App\Actions\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Traits\IPAddress;

class GetUserIPAddress extends Action{
   use IPAddress;
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    public function execute(){
       try{
         $data = [
            'real_ip' => $this->getUserIpAdress(),
            'laravel_ip' => $this->request->ip(),
            'ip_location' => $this->getUserIpLocation()
         ];
         return $this->successWithData($data);
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    