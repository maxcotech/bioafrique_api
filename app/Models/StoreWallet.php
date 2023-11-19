<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWallet extends WalletModel
{
    use HasFactory;
    protected $table = "store_wallets";
    protected $fillable = [
        'store_id', 'previous_row_hash', 'amount', 'sender_id',
        'sender_type', 'ledger_type', 'transaction_type',
        'transaction_id', 'next_row_hash', 'base_rate'
    ];

    public function getAmountAttribute($value)
    {
        return $this->convertBaseAmountByRate($value, $this->attributes['base_rate']);
    }

    public function lock()
    {
        return $this->hasOne(OrderFundLock::class, 'wallet_fund_id');
    }
}
