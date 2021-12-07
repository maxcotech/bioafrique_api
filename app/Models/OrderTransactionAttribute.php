<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransactionAttribute extends Model
{
    use HasFactory,HasRateConversion;
    protected $table = "order_transaction_attributes";
    protected $fillable = [
        'delivery_duration',
        'order_transaction_id','store_id','shipping_fee','cart_amount'
    ];
    public function items(){
        return $this->hasMany(OrderTransactionAttributeItem::class,"attribute_id");
    }
    public function setShippingFeeAttribute($value){
        $this->attributes['shipping_fee'] = $this->userToBaseCurrency($value);
    }
    public function setCartAmountAttribute($value){
        $this->attributes['cart_amount'] = $this->userToBaseCurrency($value);
    }
}
