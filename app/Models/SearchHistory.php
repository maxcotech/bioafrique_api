<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
     use HasFactory;
     protected $fillable = ['query', 'auth_type', 'user_id'];
     protected $table = "search_history";
}
