<?php

namespace App\Models;

use App\Traits\HasRateConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransactionAttributeItem extends Model
{
    use HasFactory,HasRateConversion;

    protected $table = "order_transaction_attribute_items";
    protected $fillable = [
        'attribute_id','item_id','variant_id','item_type',
        'quantity','total_amount'
    ];

    public function setTotalAmountAttribute($value){
        $this->attributes['total_amount'] = $this->userToBaseCurrency($value);
    }
    public function getTotalAmountAttribute($value){
        return $this->baseToUserCurrency($value);
    }
}
