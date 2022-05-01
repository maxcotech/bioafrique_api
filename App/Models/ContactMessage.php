<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';
    protected $fillable = ['email_address','message','seen'];

    public function getCreatedAtAttribute($value){
        $cdate = new Carbon($value);
        return  [
            'date' => $cdate->toFormattedDateString(),
            'time' => $cdate->diffForHumans()
        ];
    }

    public function getUpdatedAtAttribute($value){
        $cdate = new Carbon($value);
        return $cdate->toFormattedDateString();
    }
}
