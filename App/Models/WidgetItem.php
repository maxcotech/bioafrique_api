<?php

namespace App\Models;

use App\Traits\FilePath;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WidgetItem extends Model
{
    use HasFactory,FilePath;
    protected $table = "widget_items";
    protected $fillable = [
        'widget_id','item_title','item_image_url','item_link'
    ];

    public function getItemImageUrlAttribute($value){
        return $this->getRealPath($value);
    }
}
