<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory,FilePath;
    protected $table = "countries";
    protected $fillable = [
        'country_code','country_name','country_logo',
        'country_tel_code'
    ];
    
    public function currencies(){
        return $this->hasMany(Currency::class,"country_id");
    }
    
    public function getCountryLogoAttribute($value){
        if(isset($value)){
            return $this->getRealPath($value);
        }
        return null;
    }
}
