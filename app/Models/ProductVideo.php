<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVideo extends Model
{
    use HasFactory;

    protected $table = 'product_videos';
    protected $fillable = ['product_id','video_url','store_id'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
