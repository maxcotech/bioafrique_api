<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWish extends Model
{
    use HasFactory;
    protected $table = "product_wishes";
    protected $fillable = ['user_id','user_type','product_id','variation_id','product_type'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    public function variation(){
        return $this->belongsTo(ProductVariation::class,"variation_id");
    }
}
