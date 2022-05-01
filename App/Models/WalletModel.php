<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletModel extends Model
{
    use HasFactory,HasRateConversion;
    public const LEDGER_CREDIT = 1;
    public const LEDGER_DEBIT = 0;

    public const SUPER_ADMIN_WALLET = 1;
    public const STORE_WALLET = 2;
    public const USER_WALLET = 3;

    public function getAmountAttribute($value){
        return $this->baseToUserCurrency($value);
    }
    public function setAmountAttribute($value){
        $this->attributes['amount'] = $this->userToBaseCurrency($value);
    }

    public static function getTransactionType($transaction_type){
        switch($transaction_type){
            case OrderTransaction::class: return "Order Transaction";
            default: return "Miscellenous";
        }
    }
    public static function getLedgerTypeText($ledger_type){
        switch($ledger_type){
            case self::LEDGER_CREDIT: return "Credit";
            case self::LEDGER_DEBIT: return "Debit";
            default: return "UnKnown";
        }
    }
    public static function getSenderEmail($sender_type,$sender_id){
        switch($sender_type){
            case User::class:
                $user = User::find($sender_id);
                if(isset($user)) return $user->email;
                return "N/A";
            case Store::class:
                $store = Store::find($sender_id);
                if(isset($store)) return $store->store_email;
            default: return "N/A";
        }
    }
    public static function getSenderTypeText($sender_type){
        switch($sender_type){
            case User::class: return "User Account";
            case Store::class: return "Store Account";
            default: return "N/A";
        }
    }

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
}
