<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingLocation extends Model
{
    use HasFactory;

    protected $table = 'shipping_locations';
    protected $fillable = [
        'store_id','shipping_group_id','country_id',
        'state_id','city_id'
    ];

    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }
    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
}
