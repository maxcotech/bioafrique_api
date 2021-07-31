<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'stores';
    protected $fillable = ['user_id','store_name','slug','store_logo','country_id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function products(){
        return $this->hasMany(Product::class,'store_id');
    }
    public function images(){
        return $this->hasMany(ProductImage::class,'images_id');
    }
}
