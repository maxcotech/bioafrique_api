<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactionAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_transaction_attributes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_transaction_id');
            $table->bigInteger('store_id');
            $table->double('shipping_fee');
            $table->double('cart_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_transaction_attributes');
    }
}
