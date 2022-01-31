<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory,FilePath;
    
    protected $table = "home_banners";
    protected $fillable = ['banner','banner_link'];

    public function getBannerAttribute($value){
        return $this->getRealPath($value);
    }
}
