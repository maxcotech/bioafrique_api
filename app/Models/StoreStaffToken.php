<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStaffToken extends Model
{
    use HasFactory;

    public static $expired = 1;
    public static $not_expired = 0;

    protected $table="store_staff_tokens";
    protected $fillable = ['store_id','staff_type','staff_token','expired'];
}
