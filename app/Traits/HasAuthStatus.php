<?php
namespace App\Traits;

use App\Models\Cookie;
use App\Models\User;

trait HasAuthStatus{
    use HasCookie;

    protected $auth_type = User::auth_type;
    protected $not_auth_type = Cookie::auth_type;
    
    protected function getUserAuthTypeObject($user = null): object {
        $user_acct = $user ?? request()->user();
        if(isset($user_acct)){
            return (object) ["id" => $user_acct->id,"type" => $this->auth_type];
        } else {
            $cookie = $this->getUserByCookie();
            if(isset($cookie)){
                return (object) ["id" => $cookie->id,"type" => $this->not_auth_type];
            } else {
                return null;
            }
        }
    }

}
    