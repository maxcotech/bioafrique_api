<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public const STATUS_AWAITING_SHIPPING = 3;
    
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
