<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockModel extends Model
{
    use HasFactory;
    public const STATUS_LOCKED = 0;
    public const STATUS_OPENED = 1;
    protected $appends = ['status_text'];
    public function getStatusTextAttribute(){
        switch($this->status){
            case self::STATUS_LOCKED: return "Locked";
            case self::STATUS_OPENED: return "Unlocked";
            default: return "Unlocked";
        }
    }
}
