<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationAttribute extends Model
{
    use HasFactory;

    protected $table = "variation_attributes";

    protected $fillable = ['variation_id','option_id','option_value'];
}
