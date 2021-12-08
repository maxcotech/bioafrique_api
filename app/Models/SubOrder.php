<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    use HasFactory,HasRateConversion;
    public const PAYMENT_STATUS_PAID = 1;
    public const PAYMENT_STATUS_NOT_PAID = 0;
    protected $table = "sub_orders";
    protected $fillable = [
        'order_id','user_id','store_id','amount',
        'delivery_date','status','wallet_fund_id',
        'shipping_fee','payment_status'
    ];

    public function getDeliveryDateAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->toFormattedDateString();
    }

    public function getCreatedAtAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->toFormattedDateString();
    }
    public function getUpdatedAtAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->toFormattedDateString();
    }

    public function order(){
        return $this->belongsTo(Order::class,"order_id");
    }

    public function items(){
        return $this->hasMany(OrderItem::class,"sub_order_id");
    }
    public function fundLockPassword(){
        return $this->hasOne(OrderFundLock::class,'sub_order_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function getShippingFeeAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setShippingFeeAttribute($value){
        $this->attributes['shipping_fee'] = $this->userToBaseCurrency($value);
    }
    public function getAmountAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setAmountAttribute($value){
        $this->attributes['amount'] = $this->userToBaseCurrency($value);
    }
    

}
