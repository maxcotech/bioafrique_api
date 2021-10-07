<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('store_id');
            $table->string('variation_image');
            $table->string('variation_name')->nullable();
            $table->string('variation_sku')->nullable();
            $table->double('regular_price')->nullable();
            $table->double('sales_price')->nullable();
            $table->integer('amount_in_stock')->nullable();
            $table->tinyInteger('variation_status');
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
        Schema::dropIfExists('product_variations');
    }
}
