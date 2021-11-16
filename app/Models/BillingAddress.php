<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{
    use HasFactory;

    protected $table = "billing_addresses";
    protected $fillable=[
        'user_id','country_id','state_id','city_id',
        'street_address','phone_number','telephone_code',
        'additional_number','additional_telephone_code',
        'postal_code','is_current'
    ];
}
