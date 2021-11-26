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
            $table->bigInteger('user_id');
            $table->string('user_type',60)->default('App\Model\Cookie');
            $table->bigInteger('item_id');
            $table->bigInteger('variant_id')->nullable();
            $table->bigInteger('store_id');
            $table->string('item_type',60);
            $table->integer('quantity');
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
