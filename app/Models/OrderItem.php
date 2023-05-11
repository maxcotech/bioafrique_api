<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory,HasRateConversion;
    protected $table = "order_items";
    protected $fillable = [
        'user_id','sub_order_id','order_id','product_id',
        'variation_id','product_type','quantity','paid_amount',
    ];

    public function product(){
        return $this->belongsTo(Product::class,"product_id");
    }
    public function variation(){
        return $this->belongsTo(ProductVariation::class,"variation_id");
    }
    public function getPaidAmountAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setPaidAmountAttribute($value){
        $this->attributes['paid_amount'] = $this->userToBaseCurrency($value);
    }
}
