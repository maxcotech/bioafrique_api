<?php

namespace App\Models;

use App\Traits\FilePath;
use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory,FilePath,HasRateConversion;

    protected $table = "categories";
    protected $fillable = ['category_title','display_title','category_image',
    'category_slug','parent_id','category_level','category_icon','commission_fee'];

    public function products(){
        return $this->belongsToMany(Product::class,'product_category','category_id','product_id')
        ->withTimestamps();
    }
    public function getCategoryImageAttribute($value){
        if(!isset($value)) return null;
        return $this->getRealPath($value);
    }
    public function getCategoryIconAttribute($value){
        if(!isset($value)) return null;
        return $this->getRealPath($value);
    }

    public function getImageThumbnailAttribute($val){
        if(!isset($val)) return null;
        return $this->getRealPath($val);
    }

    public function getCommissionFeeAttribute($value){
        return $this->baseToUserCurrency($value);
    }

    public function setCommissionFeeAttribute($value){
        $this->attributes['commission_fee'] = $this->userToBaseCurrency($value);
    }
}
