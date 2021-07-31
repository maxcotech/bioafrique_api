<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessCookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //1 = active 2 = expired
        Schema::create('cookies', function (Blueprint $table) {
            $table->id();
            $table->timestamp('expiry');
            $table->tinyInteger('status')->default(1);
            $table->string('cookie_name');
            $table->string('cookie_value',500)->unique();
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
        Schema::dropIfExists('access_cookies');
    }
}
