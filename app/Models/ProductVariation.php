<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory,FilePath;

    protected $table = 'product_variations';
    protected $fillable = [
        'product_id','store_id','variation_name','variation_sku',
        'regular_price','sales_price','amount_in_stock','variation_status',
        'variation_image'
    ];

    public function getVariationImageAttribute($value){
        return $this->getRealPath($value);
    }
}
