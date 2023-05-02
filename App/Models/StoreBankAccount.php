<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreBankAccount extends Model
{
    use HasFactory;
    protected $table = "store_bank_accounts";
    protected $fillable = [
        'store_id','bank_code','bank_name','account_number','bank_currency_id'
    ];

    public function currency(){
        return $this->belongsTo(Currency::class,"bank_currency_id");
    }

   
}
