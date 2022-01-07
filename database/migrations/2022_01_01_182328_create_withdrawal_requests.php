<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->string('reference',50)->unique();
            $table->double('amount');
            $table->bigInteger('user_id');
            $table->tinyInteger('status');
            $table->bigInteger('bank_account_id');
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
        Schema::dropIfExists('withdrawal_requests');
    }
}
