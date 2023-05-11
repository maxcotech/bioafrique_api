<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCurrency extends Model
{
    use HasFactory;
    protected $table = "user_currencies";
    protected $fillable = ['currency_id','user_currencies_id','user_currencies_type'];
    
}
