<?php

namespace App\Http\Middleware;

use App\Models\Cookie;
use App\Models\Country;
use App\Models\Currency;
use App\Models\UserCountry;
use App\Models\UserCurrency;
use App\Traits\HasHttpResponse;
use App\Traits\IPAddress;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnsureCurrencySelected
{
    use IPAddress,HasHttpResponse;
    protected $request;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::alert('Middleware: ensure currency selected');
        $this->request = $request;
        $user = $this->request->user();
        $cuser = null;
        if(!isset($user)){
            if($this->request->hasCookie('basic_access')){
                $cuser = $this->getCookieUserByValue($this->request->cookie('basic_access'));
            } else {
                $access_header = $this->request->header('X-basic_access');
                if(isset($access_header)){
                    $cuser = $this->getCookieUserByValue($access_header);
                }
            }
            if(isset($cuser)){
                $this->setUserCurrencyByModel($cuser,"App\Models\Cookie");
            } else {
                return $this->notAuthorized('Could not persist your preferences, please ensure you allow cookies.');
            }
        } else {
            $this->setUserCurrencyByModel($user,'App\Models\User');
        }
        return $next($this->request);
    }

    protected function getCookieUserByValue($value){
        return Cookie::where('cookie_value',$value)
        ->where('cookie_name','basic_access')->first();
    }

    protected function isCurrencySet($model,$type){
        $exists = UserCurrency::where('user_currencies_id',$model->id)
        ->where('user_currencies_type',$type)
        ->exists();
        return $exists;
    }

    protected function setDefaultCurrency($model,$type){
        $base_currency = Currency::where('is_base_currency',1)->first();
        if(isset($base_currency)){
            UserCurrency::create([
                'currency_id' => $base_currency->id,'user_currencies_id' => $model->id,'user_currencies_type' => $type
            ]);
        }
    }

    protected function setUserCurrencyByModel($model,$type){
        if(!$this->isCurrencySet($model,$type)){
            $location = $this->getUserIpLocation();
            Log::alert(json_encode($location));
            DB::transaction(function()use($location,$model,$type){
                if(isset($location)){
                    $country = Country::where('country_code',strtoupper($location->country_code))->first();
                    $this->setUserCountry($model,$type,$country);
                    $currency = isset($country)? $country->currencies()->first():null;
                    if(isset($currency)){
                        UserCurrency::create([
                            'currency_id' => $currency->id,'user_currencies_id' => $model->id,'user_currencies_type' => $type
                        ]);
                    } else {
                        $this->setDefaultCurrency($model,$type);
                    }
                } else {
                    $this->setDefaultCurrency($model,$type);
                }
            });
        } 
        
    }

    protected function setUserCountry($model,$type,$country){
        $does_exist = UserCountry::where('user_countries_id',$model->id)
        ->where('user_countries_type',$type)->exists();
        if(!$does_exist && isset($country)){
            UserCountry::create([
                'country_id' => $country->id,
                'user_countries_id' => $model->id,
                'user_countries_type' => $type
            ]);
        }
    }
}
