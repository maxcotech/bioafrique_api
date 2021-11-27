<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWish extends Model
{
    use HasFactory;
    protected $table = "product_wishes";
    protected $fillable = ['user_id','user_type','product_id','variation_id','product_type'];
}
