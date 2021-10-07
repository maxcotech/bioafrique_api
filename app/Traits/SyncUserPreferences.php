<?php
namespace App\Traits;

use App\Models\UserCountry;
use App\Models\UserCurrency;

trait SyncUserPreferences{
    protected $old_type = "App\Models\Cookie";
    protected $new_type = "App\Models\User";

    protected function syncUserCountry($cookie_id,$user_id){
        UserCountry::where('user_countries_id',$user_id)->delete();
        UserCountry::where('user_countries_id',$cookie_id)
        ->where('user_countries_type',$this->old_type)->update([
            'user_countries_id' => $user_id,
            'user_countries_type' => $this->new_type
        ]);
    }

    protected function syncUserCurrency($cookie_id,$user_id){
        UserCurrency::where('user_currencies_id',$user_id)->delete();
        UserCurrency::where('user_currencies_id',$cookie_id)
        ->where('user_currencies_type',$this->old_type)->update([
            'user_currencies_id' => $user_id,
            'user_currencies_type' => $this->new_type
        ]);
    }

    protected function syncUserCart($cookie_id,$user_id){
        //to be implemented 
    }

    protected function syncUserRecentlyViewed($cookie_id,$user_id){
        //to be implemented 
    }

}
    