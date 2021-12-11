<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public const STATUS_PENDING = 1;
    public const STATUS_AWAITING_FULFILLMENT = 2;
    public const STATUS_AWAITING_SHIPPING = 3;
    public const STATUS_PARTIALLY_SHIPPED = 4;
    public const STATUS_SHIPPED = 5;
    public const STATUS_AWAITING_PICKUP = 6;
    public const STATUS_COMPLETED = 7;
    public const STATUS_CANCELLED = 8;
    public const STATUS_DISPUTED = 9;
    public const STATUS_AWAITING_REFUND = 10;
    public const STATUS_REFUNDED = 11;
    
    protected $table = "orders";
    protected $fillable = ['user_id','billing_address_id','order_number','total_amount','status','transaction_id'];
    
    public function getCreatedAtAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->toFormattedDateString();
    }
    public function subOrders(){
        return $this->hasMany(SubOrder::class,'order_id');
    }
    public function items(){
        return $this->hasMany(OrderItem::class,'order_id');
    }
    public function billingAddress(){
        return $this->belongsTo(BillingAddress::class,'billing_address_id');
    }
    
}
