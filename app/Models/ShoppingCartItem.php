<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartItem extends Model
{
    use HasFactory;

    protected $table = "shopping_cart_items";
    protected $fillable = [
        'user_id','user_type','item_id','item_type','quantity','total_price'
    ];

    public function itemable(){
        return $this->morphTo(__FUNCTION__,'item_type','item_id');
    }

    public function trackable(){
        return $this->morphTo(__FUNCTION__,'user_type','user_id');
    }
    
}
