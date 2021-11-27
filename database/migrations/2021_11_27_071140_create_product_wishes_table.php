<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_wishes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('user_type',50);
            $table->bigInteger('product_id');
            $table->bigInteger('variation_id')->nullable();
            $table->string('product_type',100);
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
        Schema::dropIfExists('product_wishes');
    }
}
