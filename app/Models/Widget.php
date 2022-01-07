<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    public const TYPE_SINGLE_ITEM = 1;
    public const TYPE_FOUR_ITEM = 4;
    public const TYPE_MULTI_ITEM = 10;
    public const MIN_MULTI_ITEM_COUNT = 6;

    protected $table = "widgets";
    protected $fillable = [
        'widget_title','widget_link_address','widget_link_text',
        'widget_type','index_no','status','is_block'
    ];

    public function items(){
        return $this->hasMany(WidgetItem::class,'widget_id');
    }

    public function getWidgetTypeTextAttribute(){
        switch($this->widget_type){
            case self::TYPE_SINGLE_ITEM: return "Single Item";
            case self::TYPE_FOUR_ITEM: return "Four Items";
            case self::TYPE_MULTI_ITEM: return "Multiple Items";
            default: return "Not Supported";
        }
    }

    public function getIsBlockTextAttribute(){
        switch($this->is_block){
            case 1: return "Block Type";
            case 0: return "Inline Type";
            default: return "Not Supported";
        }
    }
}
