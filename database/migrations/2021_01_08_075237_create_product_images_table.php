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
        /**
         * image_type:
         * 1 = front_image, 2 = back_image,
         * 3 = side_image, 4 = gallery_one,
         * 5 = gallery_two, 6 = gallery_three
         */
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->bigInteger('product_id');
            $table->string('image_type',50)->default('front_image');
            $table->string('image_url');
            $table->string('image_thumbnail')->nullable();
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
