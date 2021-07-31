<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //image_type 1 = main_image, 2 product_gallery 3 = variation_image
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->bigInteger('product_id');
            $table->tinyInteger('image_type')->default(1);
            $table->string('alt_text')->nullable();
            $table->string('image_url');
            $table->string('image_thumbnail');
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
        Schema::dropIfExists('product_images');
    }
}
