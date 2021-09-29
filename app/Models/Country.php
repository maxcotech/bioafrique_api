<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = "countries";
    protected $fillable = [
        'country_code','country_name','country_logo',
        'country_tel_code'
    ];
    
    public function currencies(){
        return $this->hasMany(Currency::class,"country_id");
    }
}
