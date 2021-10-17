<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreStaffTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * staff type can be worker or manager
     */
    /**
     * expired: 1=expired 0=not expired;
     */
    public function up()
    {
        Schema::create('store_staff_tokens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->tinyInteger('staff_type');
            $table->string('staff_token',1000);
            $table->tinyInteger('expired')->default(0);
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
        Schema::dropIfExists('store_staff_tokens');
    }
}
