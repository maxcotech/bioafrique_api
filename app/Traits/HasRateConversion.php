<?php
namespace App\Traits;

use App\Models\Currency;
use Illuminate\Support\Facades\Auth;

trait HasRateConversion{
    use HasCookie;
    protected $user_currency;
    protected $base_currency;
    protected function getBaseCurrency(){
        if($this->base_currency == null){
            $this->base_currency = Currency::where('is_base_currency',1)->first();
        }
        return $this->base_currency;
    }

    protected function getUserCurrency($user_acct = null,$cookie_acct = null){
        if($this->user_currency !== null){
            //Log::alert('retrieving user currencies from memory');
            return $this->user_currency;
        } else {
            //Log::alert('retrieving user currencies from db');
            $user = isset($user_acct)? $user_acct : Auth::user();
            $this->user_currency = isset($user)? $user->currency()->first():null;
            if($this->user_currency !== null){
                return $this->user_currency;
            } else {
                $cuser = isset($cookie_acct)? $cookie_acct : $this->getUserByCookie();
                $this->user_currency = isset($cuser)? $cuser->currency()->first():null;
                return ($this->user_currency)? $this->user_currency: $this->getBaseCurrency();
            }
        }
    }

    protected function userToBaseCurrency($amount,$user = null,$cookie = null){
        $user_currency = $this->getUserCurrency($user,$cookie);
        return round($amount / $user_currency->base_rate,2);
    }

    protected function convertBaseAmountByRate($amount,$rate){
        return round($amount * $rate,2);
    }

    protected function baseToUserCurrency($amount,$user = null,$cookie = null){
        $user_currency = $this->getUserCurrency($user,$cookie);
        return round($amount * $user_currency->base_rate,2);
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
    