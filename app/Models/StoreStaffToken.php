<?php

namespace App\Models;

use App\Traits\HasRoles;
use App\Traits\HasStoreRoles;
use App\Traits\StringFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStaffToken extends Model
{
    use HasFactory,HasStoreRoles,StringFormatter;

    public static $expired = 1;
    public static $not_expired = 0;
    protected $appends = ['staff_type_text'];

    protected $table="store_staff_tokens";
    protected $fillable = ['store_id','staff_type','staff_token','expired'];

    public function getStaffTypeTextAttribute(){
        return $this->capitalizeByDelimiter($this->getStoreRoleText($this->staff_type),'_');
    }
    public function getCreatedAtAttribute($value){
        $carbon = new Carbon($value);
        return $carbon->diffForHumans();
    }
}
