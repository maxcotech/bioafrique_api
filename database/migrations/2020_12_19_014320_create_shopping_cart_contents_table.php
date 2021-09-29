<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('shopping_cart_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shopping_cart_id');
            $table->bigInteger('item_id');
            $table->bigInteger('variant_id')->nullable();
            $table->string('item_type',60);
            $table->integer('quantity');
            $table->double('total_price');
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
        Schema::dropIfExists('shopping_cart_contents');
    }
}
