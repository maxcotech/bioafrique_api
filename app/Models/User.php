<?php

namespace App\Models;

use App\Traits\HasPermissions;
use App\Traits\HasRoles;
use App\Traits\HasUserStatus;
use App\Traits\StringFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens,HasUserStatus,HasRoles,StringFormatter,HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public const auth_type = "App\Models\User";
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

    public function getAccountStatusTextAttribute(){
        return $this->getUserStatusText($this->account_status);
    }

    public function getUserTypeTextAttribute(){
        return $this->capitalizeByDelimiter(
            $this->getRoleTextById($this->user_type),
            "_"
        );
    }
    public function userDevice(){
        return $this->hasOne(UserDevice::class,'user_id');
    }
    public function shoppingCarts(){
        return $this->morphMany(ShoppingCartItem::class,'trackable');
    }
    public function currency(){
        return $this->morphToMany(Currency::class,'user_currencies');
    }
    public function country(){
        return $this->morphToMany(Country::class,'user_countries');
    }
    public function store(){
        return $this->hasOne(Store::class,'user_id');
    }
    public function authAccessTokens(){
        return $this->hasMany(OauthAccessToken::class,'user_id');
    }
    public function workStores(){
        return $this->belongsToMany(Store::class,'store_staffs','user_id','store_id');
    }
    public function storeStaffAccounts(){
        return $this->hasMany(StoreStaff::class,'user_id');
    }

    public function billingAddresses(){
        return $this->hasMany(BillingAddress::class,"user_id");
    }

    public function getCreatedAtAttribute($value){
        $date = new Carbon($value);
        return $date->toFormattedDateString();
    }
    public function getUpdatedAtAttribute($value){
        $date = new Carbon($value);
        return $date->toFormattedDateString();
    }


    public function permissions(){
        return $this->belongsToMany(Permission::class,"user_permissions","user_id","permission_id");
    }
}
