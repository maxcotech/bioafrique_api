<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $table = "currencies";
    protected $fillable = [
        'country_id','is_base_currency',
        'currency_name','currency_code',
        'currency_sym','base_rate'
    ];
}
