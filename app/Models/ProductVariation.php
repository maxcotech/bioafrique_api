<?php

namespace App\Models;

use App\Traits\FilePath;
use App\Traits\HasRateConversion;
use App\Traits\HasResourceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory,FilePath,HasRateConversion,HasResourceStatus;
    protected $appends = ['current_price'];
    protected $table = 'product_variations';
    protected $fillable = [
        'product_id','store_id','variation_name','variation_sku',
        'regular_price','sales_price','amount_in_stock','variation_status',
        'variation_image'
    ];

    public function getReviewAverageAttribute(){
        $reviews = ProductReview::where('product_id',$this->product_id)
        ->where('variation_id',$this->id)
        ->where('product_type',Product::variation_product_type)
        ->where('status',$this->getResourceActiveId())->get();
        if(count($reviews) > 0){
            return $this->getReviewAverage($reviews,'star_rating');
        }
        return 0;
    }
    public function getReviewSummaryAttribute(){
        $reviews = ProductReview::where('product_id',$this->product_id)
        ->where('variation_id',$this->id)
        ->where('product_type',Product::variation_product_type)
        ->where('status',$this->getResourceActiveId())->get();
        return $this->getReviewSummary($reviews,"star_rating");
    }

    public function getCurrentPriceAttribute(){
        if($this->sales_price == 0 || $this->sales_price == null){
            return $this->regular_price;
        }
        return $this->sales_price;
    }

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
