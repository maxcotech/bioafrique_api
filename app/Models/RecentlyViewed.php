<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentlyViewed extends Model
{
    use HasFactory;
    protected $table = "recently_viewed";
    protected $fillable = ['id','product_id','user_id','user_type'];
}
