<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactionAttributeItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_transaction_attribute_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attribute_id');
            $table->bigInteger('item_id');
            $table->bigInteger('variant_id')->nullable();
            $table->string('item_type');
            $table->integer('quantity');
            $table->double('total_amount');
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
        Schema::dropIfExists('order_transaction_attribute_items');
    }
}
