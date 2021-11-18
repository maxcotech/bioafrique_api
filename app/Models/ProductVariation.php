<?php

namespace App\Models;

use App\Traits\FilePath;
use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory,FilePath,HasRateConversion;

    protected $table = 'product_variations';
    protected $fillable = [
        'product_id','store_id','variation_name','variation_sku',
        'regular_price','sales_price','amount_in_stock','variation_status',
        'variation_image'
    ];

    public function getRegularPriceAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function getSalesPriceAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setSalesPriceAttribute($value){
        $this->attributes['sales_price'] = $this->userToBaseCurrency($value);
    }

    public function setRegularPriceAttribute($value){
        $this->attributes['regular_price'] = $this->userToBaseCurrency($value);
    }

    public function getVariationImageAttribute($value){
        return $this->getRealPath($value);
    }

    public function options(){
        return $this->hasMany(VariationAttributes::class,'variation_id');
    }
}
