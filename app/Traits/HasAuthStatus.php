<?php
namespace App\Traits;


trait HasAuthStatus{
    use HasCookie;

    protected $auth_type = "App\Model\User";
    protected $not_auth_type = "App\Model\Cookie";
    

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
    