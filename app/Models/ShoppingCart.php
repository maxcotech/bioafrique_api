<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    protected $table = 'shopping_carts';
    protected $fillable = ['user_id','user_type','status','expiry'];

    public function trackable(){
        return $this->morphTo(__FUNCTION__,'user_type','user_id');
    }
    public function items(){
        return $this->hasMany(ShoppingCartItem::class,'shopping_cart_id');
    }
    
}
