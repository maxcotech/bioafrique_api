<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory,FilePath,FilePath;
    protected $table = 'stores';
    protected $with = ['state','city'];
    protected $fillable = [
        'user_id','store_name','store_slug','store_logo',
        'country_id','store_address','store_email','store_telephone',
        'state_id','city_id'
    ];

    public function getStoreLogoAttribute($value){
        return $this->getRealPath($value);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function products(){
        return $this->hasMany(Product::class,'store_id');
    }
    public function images(){
        return $this->hasMany(ProductImage::class,'images_id');
    }

    public function staffTokens(){
        return $this->hasMany(StoreStaffToken::class,'store_id');
    }
    
    public function staffs(){
        return $this->hasMany(StoreStaff::class,'store_id');
    }

    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }

    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }

}
