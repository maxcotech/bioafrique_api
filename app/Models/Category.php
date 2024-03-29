<?php

namespace App\Models;

use App\Traits\FilePath;
use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory,FilePath,HasRateConversion;

    public const MAIN_CATEGORY_LEVEL = 1;
    public const SUB_CATEGORY_LEVEL = 2;
    public const SUB_SUB_CATEGORY_LEVEL = 3;

    protected $table = "categories";
    protected $fillable = [
    'category_title','display_title','category_image','status',
    'category_slug','parent_id','category_level','category_icon','commission_fee'];

    public function products(){
        return $this->belongsToMany(Product::class,'product_category','category_id','product_id')
        ->withTimestamps();
    }

    public function subCategories(){
        return $this->hasMany(Category::class,'parent_id');
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

}
