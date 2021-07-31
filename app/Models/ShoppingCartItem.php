<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartItem extends Model
{
    use HasFactory;

    protected $table = "shopping_cart_items";
    protected $fillable = ['shopping_cart_id','item_id','item_type','quantity','total_price'];

    public function itemable(){
        return $this->morphTo(__FUNCTION__,'item_type','item_id');
    }
    public function shoppingCart(){
        return $this->belongsTo(ShoppingCart::class,'shopping_cart_id');
    }
}
