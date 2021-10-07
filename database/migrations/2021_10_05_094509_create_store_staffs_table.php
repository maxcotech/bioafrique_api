<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     /**
      * staff_type: store_owner = 12, store_manager = 11, store_worker = 10
      */
    public function up()
    {
        Schema::create('store_staffs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->bigInteger('user_id');
            $table->tinyInteger('staff_type');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('store_staffs');
    }
}
