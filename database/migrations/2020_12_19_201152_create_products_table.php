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
        //availability 1 = Readily available, 2 = specify available , 3 = not available
        //product_status 0 = not approved 1 = approved, 2 = in draft 4 = blacklisted
        //product_type 1 = simple product, 2 = variation product
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('category_id');
            $table->string('product_name');
            $table->tinyInteger('product_type')->default(1);
            $table->integer('amount_in_stock')->nullable();
            $table->string('product_slug')->unique();
            $table->string('product_sku')->nullable();
            $table->string('simple_description')->nullable();
            $table->text('description')->nullable();
            $table->text("key_features")->nullable();
            $table->string('dimensions')->nullable();
            $table->double("weight");
            $table->string('youtube_video_id',1000)->nullable();
            $table->tinyInteger('product_status');
            $table->double('regular_price');
            $table->double('sales_price')->nullable();
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
