<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperAdminWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('super_admin_wallet', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->string('previous_row_hash')->nullable();
            $table->bigInteger('sender_id');
            $table->string('sender_type',50); // User , Store, 
            $table->bigInteger('ledger_type'); // credit = 1, debit = 0
            $table->string('transaction_type',50)->nullable(); //order, transfer , bonus, gift;
            $table->bigInteger('transaction_id')->nullable(); //primary key of table containing the specified transaction type.
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
        Schema::dropIfExists('super_admin_wallet');
    }
}
