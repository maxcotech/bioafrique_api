<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStaff extends Model
{
    use HasFactory;

    protected $table = "store_staffs";

    protected $fillable = ['store_id','user_id','staff_type','status'];

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}
