<?php

namespace App\Models;

use App\Traits\FilePath;
use App\Traits\HasDataProcessing;
use App\Traits\HasRateConversion;
use App\Traits\HasResourceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,FilePath,HasRateConversion,HasDataProcessing,HasResourceStatus;

    public const simple_product_type = "simple_product";
    public const variation_product_type = "variation_product";
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

    
    protected $appends = ['current_price','review_average'];

    public function getCurrentPriceAttribute(){
        if($this->sales_price == 0 || $this->sales_price == null){
            return $this->regular_price;
        }
        return $this->sales_price;
    }

    public function variations(){
        return $this->hasMany(ProductVariation::class,'product_id');
    }

    public function categories(){
        return $this->belongsToMany(Category::class,'product_category','product_id','category_id')
        ->withTimestamps();
    }
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function store(){
        return $this->belongsTo(Store::class,"store_id");
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

    public function reviews(){
        return $this->hasMany(ProductReview::class,'product_id');
    }

    public function getReviewAverageAttribute(){
        $reviews = ProductReview::where('product_id',$this->id)
        ->where('status',$this->getResourceActiveId())->get();
        if(count($reviews) > 0){
            return $this->getReviewAverage($reviews,'star_rating');
        }
        return 0;
    }
    public function getReviewSummaryAttribute(){
        $reviews = ProductReview::where('product_id',$this->id)
        ->where('status',$this->getResourceActiveId())->get();
        return $this->getReviewSummary($reviews,"star_rating");
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
        $this->attributes['regular_price'] = $this->userToBaseCurrency($value);
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
