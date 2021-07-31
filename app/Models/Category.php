<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory,FilePath;

    protected $table = "categories";
    protected $fillable = ['category_title','display_title','category_image',
    'category_slug','parent_id','category_level','image_thumbnail'];

    public function products(){
        return $this->belongsToMany(Product::class,'product_category','category_id','product_id')
        ->withTimestamps();
    }
    public function getCategoryImageAttribute($value){
        return $this->getRealPath($value);
    }
    public function getImageThumbnailAttribute($val){
        return $this->getRealPath($val);
    }
}
