<?php
namespace App\Traits;

use App\Models\Cookie;

trait HasCookie{

    protected $access_cookie_key = "basic_access";

    protected function getUserByCookie(){
        if($this->request->hasCookie($this->access_cookie_key)){
            $cookie_value = $this->request->cookie($this->access_cookie_key);
            $cookie = Cookie::where('cookie_name',$this->access_cookie_key)
            ->where('cookie_value',$cookie_value)->first();
            return $cookie;
        }
        return null;
    }

   


}
    