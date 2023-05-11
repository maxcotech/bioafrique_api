<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingGroup extends Model
{
    use HasFactory,HasRateConversion;

    protected $table = "shipping_groups";
    protected $fillable = [
        'store_id','group_name','shipping_rate',
        'high_value_rate','mid_value_rate',
        'low_value_rate','dimension_range_rates',
        'delivery_duration','door_delivery_rate'
    ];

    public function shippingLocations(){
        return $this->hasMany(ShippingLocation::class,'shipping_group_id');
    }

    public function setShippingRateAttribute($value){
        $this->attributes['shipping_rate'] = $this->userToBaseCurrency($value);
    }

    public function getShippingRateAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setHighValueRateAttribute($value){
        $this->attributes['high_value_rate'] = $this->userToBaseCurrency($value);
    }

    public function getHighValueRateAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setMidValueRateAttribute($value){
        $this->attributes['mid_value_rate'] = $this->userToBaseCurrency($value);
    }

    public function getMidValueRateAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setLowValueRateAttribute($value){
        $this->attributes['low_value_rate'] = $this->userToBaseCurrency($value);
    }

    public function getLowValueRateAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setDoorDeliveryRateAttribute($value){
        $this->attributes['door_delivery_rate'] = $this->userToBaseCurrency($value);
    }

    public function getDoorDeliveryRateAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setDimensionRangeRatesAttribute($value){
        $this->attributes['dimension_range_rates'] = $this->convertNestedRates(
            $value,['rate'],
            function($rate){ return $this->userToBaseCurrency($rate); },
            true
        );
    }

    public function getDimensionRangeRatesAttribute($value){
        return $this->convertNestedRates(
            $value,['rate'],
            function($rate){ return $this->baseToUserCurrency($rate);},
            true
        );
    }
}
