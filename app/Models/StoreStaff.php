<?php

namespace App\Models;

use App\Traits\HasStoreRoles;
use App\Traits\HasUserStatus;
use App\Traits\StringFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStaff extends Model
{
    use HasFactory,HasUserStatus,HasStoreRoles,StringFormatter;

    protected $table = "store_staffs";

    protected $fillable = ['store_id','user_id','staff_type','status'];
    protected $appends = ['staff_type_text','status_text'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function getStaffTypeTextAttribute(){
        return $this->capitalizeByDelimiter($this->getStoreRoleText($this->staff_type),"_");
    }

    public function getStatusTextAttribute(){
        return $this->capitalizeByDelimiter($this->getUserStatusText($this->status),"_");
    }
}
