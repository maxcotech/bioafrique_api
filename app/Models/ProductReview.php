<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    protected $table = "product_reviews";
    protected $fillable = [
        'product_id','variation_id','user_id','review_comment','star_rating'
    ];

}
