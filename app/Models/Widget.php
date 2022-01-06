<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    public const TYPE_SINGLE_ITEM = 1;
    public const TYPE_FOUR_ITEM = 2;
    public const TYPE_MULTI_ITEM = 3;
    public const MIN_MULTI_ITEM_COUNT = 6;

    protected $table = "widgets";
    protected $fillable = [
        'widget_title','widget_link_address','widget_link_text',
        'widget_type','index_no','status'
    ];
}
