<?php
namespace App\Actions\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Cookie;

class GetUserProfile extends Action{
    protected $request;
    public function __construct(Request $request){
       $this->request=$request;
    }
    public function execute(){
       try{
          $data = [];
          $cookie = Cookie::where('cookie_value',$this->request->cookie('basic_access'))->first();
          $data['currency'] =  $cookie->currency()->first();
          $data['country'] = $cookie->country()->first();
          return $this->successWithData($data);
       }
       catch(\Exception $e){
          return $this->internalError($e->getMessage());
       }
    }

}
    