<?php

namespace App\Models;

use App\Traits\HasPayment;
use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    use HasFactory,HasPayment,HasRateConversion;
    public const STATUS_PENDING = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_VERIFIED = 2;
    public const STATUS_CANCELLED = 3;

    //payment gateways 

    public const FLUTTERWAVE = 1;
    public const PAYSTACK = 2;

    public $table = "order_transactions";
    public $fillable = [
        'order_id','reference','gateway_reference','status','amount',
        'currency_id','user_id','payment_gateway'
    ];
    protected $appends = ['payment_gateway_text','status_text'];

    public function getPaymentGatewayTextAttribute(){
        if($this->payment_gateway != null){
            return $this->gateways[$this->payment_gateway];
        }
        return null;
    }

    public function setAmountAttribute($value){
        $this->attributes['amount'] = $this->userToBaseCurrency($value);
    }

    public function getStatusTextAttribute(){
        if($this->status != null){
            return $this->payment_status_list[$this->status];
        }
        return null;
    }

    public function attributes(){
        return $this->hasMany(OrderTransactionAttribute::class,"order_transaction_id");
    }
    
}
