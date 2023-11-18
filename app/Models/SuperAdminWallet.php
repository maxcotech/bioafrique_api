<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuperAdminWallet extends WalletModel
{
    use HasFactory;
    protected $table = "super_admin_wallet";
    protected $fillable = [
        'amount', 'previous_row_hash', 'sender_id', 'sender_type',
        'ledger_type', 'transaction_type', 'transaction_id',
        'next_row_hash', 'base_rate'
    ];

    public function orderCommissionLock()
    {
        return $this->hasOne(OrderCommissionLock::class, 'wallet_fund_id');
    }
}
