<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type',
        'account_status',
        'auth_type',
        'phone_number',
        'telephone_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getFullNameAttribute($value){
        return $this->first_name." ".$this->last_name;
    }
    public function userDevice(){
        return $this->hasOne(UserDevice::class,'user_id');
    }
    public function shoppingCarts(){
        return $this->morphMany(ShoppingCart::class,'trackable');
    }
    public function currency(){
        return $this->morphToMany(Currency::class,'user_currencies')->latestOfMany();
    }
    public function country(){
        return $this->morphToMany(Country::class,'user_countries')->latestOfMany();
    }
}
