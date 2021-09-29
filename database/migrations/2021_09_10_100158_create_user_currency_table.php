<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_currencies', function (Blueprint $table) {
            $table->id();
            $table->integer('currency_id');
            $table->bigInteger('user_currencies_id');
            $table->string('user_currencies_type')->default('App\Model\Cookie');
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
        Schema::dropIfExists('user_currencies');
    }
}
