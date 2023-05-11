<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OneTimePassword extends Model
{
    use HasFactory;
    public const purposes = ['email_verification','password_reset','number_verification'];
    public const receiver_types = ['phone_number','email_address'];
    protected $table = "one_time_passwords";
    protected $fillable = ['email','phone','password','receiver_type','expiry','purpose'];

    /**
     * Checks if one time password has expired 
     * @return bool
     */
    
    public function isExpired(){
        $expire_obj = new Carbon($this->expiry);
        if(now()->greaterThan($expire_obj)){
            //token expired 
            return true;
        }
    }



}
