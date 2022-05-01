<?php
namespace App\Traits;

use App\Models\Cookie;
use Illuminate\Support\Facades\Log;

trait HasCookie{

    protected $access_cookie_key = "basic_access";
    protected $header_access_cookie_key = "X-basic_access";
    protected function getUserByCookie(){
        $cookie_value = null;
        if(request()->hasCookie($this->access_cookie_key)){
            $cookie_value = request()->cookie($this->access_cookie_key);
           
        } else if(request()->header($this->header_access_cookie_key,null) != null){
            $cookie_value = request()->header($this->header_access_cookie_key);
        }
        if(isset($cookie_value)){
            $cookie = Cookie::where('cookie_name',$this->access_cookie_key)
            ->where('cookie_value',$cookie_value)->first();
            Log::alert('retrieved cookie from header '.json_encode($cookie));
            return $cookie;
        }
        return null;
    }

   


}
    