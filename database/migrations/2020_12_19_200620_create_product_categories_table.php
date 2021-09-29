<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * display_level: 
     * 1 = only main image required,
     * 2 = front, back image required,
     * 3 = front, back and side images required,
     * 4 = all six product images required
     */
    /**
     * status:
     * 0 = inactive 
     * 1 = active 
     * 2 = in review 
     * 4 = blacklisted
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->default(0);
            $table->string('category_title');
            $table->integer('category_level')->default(1);
            $table->string('display_title')->nullable();
            $table->string('category_slug')->unique();
            $table->string('category_image')->nullable();
            $table->string('category_icon');
            $table->double('commission_fee')->nullable();
            $table->tinyInteger('display_level')->default(1);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('categories');
    }
}
