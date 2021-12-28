<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuperAdminWallet extends WalletModel
{
    use HasFactory;
    protected $table = "super_admin_wallet";
    protected $fillable = [
        'amount','previous_row_hash','sender_id','sender_type',
        'ledger_type','transaction_type','transaction_id',
    ];


    public function getCreatedAtAttribute($value){
        $cdate = new Carbon($value);
        if(isset($cdate)){
            return $cdate->toFormattedDateString();
        } else {
            return "N/A";
        }
    }
    public function getUpdatedAtAttribute($value){
        $cdate = new Carbon($value);
        if(isset($cdate)){
            return $cdate->toFormattedDateString();
        } else {
            return "N/A";
        }
    }

    public function orderCommissionLock(){
        return $this->hasOne(OrderCommissionLock::class,'wallet_fund_id');
    }

}
