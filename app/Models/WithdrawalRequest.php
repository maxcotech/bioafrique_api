<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory,HasRateConversion;
    public const STATUS_PENDING = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_CANCELLED = 2;

    protected $table = "withdrawal_requests";
    protected $fillable = ['store_id','reference','amount','user_id','status','bank_account_id'];
    public function getAmountAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setAmountAttribute($value){
        $this->attributes['amount'] = $this->userToBaseCurrency($value);
    }
    
}
