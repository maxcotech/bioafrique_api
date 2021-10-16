<?php

namespace App\Models;

use App\Traits\FilePath;
use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,FilePath,HasRateConversion;

    const simpleProductType = 1;
    const variationProductType = 2;

    protected $table = "products";
    protected $fillable = [
        'store_id','brand_id','parent_id','regular_price','sales_price',
        'amount_in_stock',
        'stock_threshold','product_slug','product_sku',
        'simple_description','description',
        'product_type','product_status','product_image',
        'key_features','dimension_height','dimension_width',
        'dimension_length','product_name','weight','youtube_video_id',
        'category_id'
    ];

    public function variations(){
        return $this->hasMany(ProductVariation::class,'product_id');
    }

    public function categories(){
        return $this->belongsToMany(Category::class,'product_category','product_id','category_id')
        ->withTimestamps();
    }
    public function images(){
        return $this->hasMany(ProductImage::class,'product_id');
    }
    public function tags(){
        return $this->belongsToMany(ProductTag::class,'product_tags','product_id','tag_id')
        ->withTimestamps();
    }
    public function videos(){
        return $this->hasMany(ProductVideo::class,'product_id');
    }
    public function dimensions(){
        return $this->hasOne(ProductDimension::class,'product_id');
    }

    public function getProductImageAttribute($value){
        return $this->getRealPath($value);
    }

    public function getRegularPriceAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function getSalesPriceAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setRegularPriceAttribute($value){
        $this->attributes['regular_price'] = round($this->userToBaseCurrency($value));
    }

    public function setSalesPriceAttribute($value){
        $this->attributes['sales_price'] = $this->userToBaseCurrency($value);
    }

    public function getKeyFeaturesAttribute($value){
        return htmlspecialchars_decode($value);
    }

    public function getSimpleDescriptionAttribute($value){
        return htmlspecialchars_decode($value);
    }

    public function getDescriptionAttribute($value){
        return htmlspecialchars_decode($value);
    }

}
