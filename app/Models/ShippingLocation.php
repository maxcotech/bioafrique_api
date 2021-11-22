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
}
