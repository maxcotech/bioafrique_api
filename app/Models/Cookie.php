<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cookie extends Model
{
    use HasFactory;

    protected $table = "cookies";
    protected $fillable = ['cookie_name','cookie_value','expiry','status'];

    public function shoppingCarts(){
        return $this->morphMany(ShoppingCartItem::class,'trackable');
    }
    public function currency(){
        return $this->morphToMany(Currency::class,'user_currencies');
    }
    public function country(){
        return $this->morphToMany(Country::class,'user_countries');
    }

}
