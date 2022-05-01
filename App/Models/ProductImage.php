<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory,FilePath;
    protected $table = "product_images";
    protected $fillable = ['store_id','image_type','product_id','image_url','alt_text','image_thumbnail'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    public function getImageUrlAttribute($value){
        return $this->getRealPath($value);
    }
    
    
}
