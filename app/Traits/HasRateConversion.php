<?php
namespace App\Traits;

use App\Models\Currency;
use Illuminate\Support\Facades\Auth;

trait HasRateConversion{
    use HasCookie;

    protected function getBaseCurrency(){
        return Currency::where('is_base_currency',1)->first();
    }

    protected function getUserCurrency($user_acct = null,$cookie_acct = null){
        $user = isset($user_acct)? $user_acct : Auth::user();
        $currency = isset($user)? $user->currency()->first():null;
        if(isset($currency)){
            return $currency;
        } else {
            $cuser = isset($cookie_acct)? $cookie_acct : $this->getUserByCookie();
            $currency = isset($cuser)? $cuser->currency()->first():null;
            return isset($currency)? $currency: $this->getBaseCurrency();
        }
    }

    protected function userToBaseCurrency($amount,$user = null,$cookie = null){
        $user_currency = $this->getUserCurrency($user,$cookie);
        return $amount / $user_currency->base_rate;
    }

    protected function baseToUserCurrency($amount,$user = null,$cookie = null){
        $user_currency = $this->getUserCurrency($user,$cookie);
        return $amount * $user_currency->base_rate;
    }

}
    