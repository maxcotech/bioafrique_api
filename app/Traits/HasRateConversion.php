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

    protected function convertNestedRates($data,$keys,$function,$json = true){
        $new_data = [];
        $input_data = [];
        if($json === true){
            $input_data = json_decode($data,true);
        } else {
            $input_data = $data;
        }
        if(isset($input_data) && is_array($input_data) && count($input_data) > 0){
            foreach($input_data as $data_row){
                $new_row = $data_row;
                foreach($keys as $key){
                    $new_row[$key] = $function($data_row[$key]);
                }
                array_push($new_data,$new_row);
            }
        }
        return ($json === true)? json_encode($new_data) : $new_data;

    }

}
    