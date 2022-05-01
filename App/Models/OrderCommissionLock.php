<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderCommissionLock extends LockModel
{
    use HasFactory;
    protected $table = "order_commission_locks";
    protected $fillable = [
        'user_id','store_id','order_id','sub_order_id',
        'wallet_fund_id','status'
    ];
    

}
