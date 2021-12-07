<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockModel extends Model
{
    use HasFactory;
    public const STATUS_LOCKED = 0;
    public const STATUS_OPENED = 1;
}
