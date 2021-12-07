<?php

namespace App\Models;

use App\Traits\HasRateConversion;
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

    public function items(){
        return $this->hasMany(OrderItem::class,"sub_order_id");
    }
    public function fundLockPassword(){
        $user = request()->user();
        if($this->user_id == $user->id){
            return $this->belongsTo(OrderFundLock::class,'wallet_fund_id');
        }
        return null;
    }

    public function getShippingFeeAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setShippingFeeAttribute($value){
        $this->attributes['shipping_fee'] = $this->userToBaseCurrency($value);
    }
    

}
