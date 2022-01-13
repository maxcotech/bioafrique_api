<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdminPreference extends Model
{
    use HasFactory;
    public const COMMISSION_PREFERENCE = "commission_preference";

    public const initData = [
        ['key' =>  SuperAdminPreference::COMMISSION_PREFERENCE,'init_value' => 1]
    ];
    protected $table = 'super_admin_preferences';
    protected $fillable = ['preference_key','preference_value'];
}
