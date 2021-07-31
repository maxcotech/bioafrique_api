<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //status = 1 = active, 2 = processed 3 = expired
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('user_type',60)->default('App\Model\Cookie');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('expiry');
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
        Schema::dropIfExists('shopping_carts');
    }
}
