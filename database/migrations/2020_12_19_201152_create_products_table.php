<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //product_status 0 = not approved 1 = approved, 3 = blacklisted
        //product_type 1 = simple product, 2 = variation product
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('parent_id')->default(0);
            $table->double('regular_price');
            $table->double('sales_price');
            $table->string('product_name');
            $table->timestamp('sales_price_expiry')->nullable();
            $table->tinyInteger('product_type')->default(1);
            $table->integer('amount_in_stock')->nullable();
            $table->integer('stock_threshold')->default(0);
            $table->string('product_slug')->unique();
            $table->string('product_sku')->nullable();
            $table->string('simple_description')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('product_status');
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
        Schema::dropIfExists('products');
    }
}
