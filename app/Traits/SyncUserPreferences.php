<?php
namespace App\Traits;

use App\Models\Cookie;
use App\Models\ProductWish;
use App\Models\RecentlyViewed;
use App\Models\ShoppingCartItem;
use App\Models\User;
use App\Models\UserCountry;
use App\Models\UserCurrency;
use Illuminate\Support\Facades\Log;

trait SyncUserPreferences{
    protected $old_type = Cookie::auth_type;
    protected $new_type = User::auth_type;

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
        Log::alert('current cookie is '.$cookie_id);
        ShoppingCartItem::where('user_id',$cookie_id)
        ->where('user_type',$this->old_type)
        ->update([
            'user_id' => $user_id,
            'user_type' => $this->new_type
        ]);
    }

    protected function syncUserRecentlyViewed($cookie_id,$user_id){
        RecentlyViewed::where('user_type',$this->old_type)
        ->where('user_id',$cookie_id)->update([
            'user_id' => $user_id,
            'user_type' => $this->new_type
        ]);
    }

    protected function syncUserWishList($cookie_id,$user_id){
        ProductWish::where('user_type',$this->old_type)
        ->where('user_id',$cookie_id)->update([
            'user_id' => $user_id,
            'user_type' => $this->new_type
        ]);
    }

}
    