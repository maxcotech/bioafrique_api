<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    protected $fillable = ['tag'];

    public function products(){
        return $this->belongsToMany(Product::class,'product_tags','tag_id','product_id')
        ->withTimestamps();
    }
}
