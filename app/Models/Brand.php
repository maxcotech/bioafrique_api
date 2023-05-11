<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory,FilePath;

    protected $table = "brands";

    protected $fillable = ['brand_name','brand_logo','website_url','status'];

    public function getBrandLogoAttribute($value){
        if(isset($value)){
            return $this->getRealPath($value,'brands');
        } 
        return null;
    }
}
