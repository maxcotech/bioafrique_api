<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    protected $table = "product_reviews";
    protected $fillable = [
        'product_id','variation_id','user_id',
        'review_comment','star_rating','product_type',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }

    public function product(){
        return $this->belongsTo(Product::class,"product_id");
    }
    public function variation(){
        return $this->belongsTo(ProductVariation::class,"variation_id");
    }
    public function getCreatedAtAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->toFormattedDateString();
    }
    public function getUpdatedAtAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->toFormattedDateString();
    }

}
