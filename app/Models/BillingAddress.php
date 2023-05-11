<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{
    use HasFactory;

    public static $current_id = 1;
    public static $not_current_id = 0;
    protected $table = "billing_addresses";
    protected $fillable=[
        'user_id','country_id','state_id','city_id',
        'street_address','phone_number','telephone_code',
        'additional_number','additional_telephone_code',
        'postal_code','is_current','first_name','last_name'
    ];

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }

    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }

    
}
