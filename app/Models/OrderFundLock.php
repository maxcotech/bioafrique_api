<?php

namespace App\Models;

use App\Traits\HasEncryption;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderFundLock extends LockModel
{
    use HasFactory,HasEncryption;
    
    protected $table = "order_fund_locks";
    protected $fillable = [
        'user_id','store_id','order_id','sub_order_id',
        'lock_password','wallet_fund_id','status'
    ];

    public function getLockPasswordAttribute($value){
        return $this->decryptData($value,$this->user_id);
    }
}
