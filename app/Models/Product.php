<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,FilePath;

    const simpleProductType = 1;
    const variationProductType = 2;

    protected $table = "products";
    protected $fillable = [
        'store_id','brand_id','parent_id','regular_price','sales_price',
        'sales_price_expiry','amount_in_stock',
        'stock_threshold','product_slug','product_sku',
        'simple_description','description',
        'product_type','product_status','product_image',
        'key_features','dimension_height','dimension_width',
        'dimension_length'
    ];

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
    

}
