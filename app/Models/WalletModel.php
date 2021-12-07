<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletModel extends Model
{
    use HasFactory,HasRateConversion;
    public const LEDGER_CREDIT = 1;
    public const LEDGER_DEBIT = 0;

    public function getAmountAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setAmountAttribute($value){
        $this->attributes['amount'] = $this->userToBaseCurrency($value);
    }
}
