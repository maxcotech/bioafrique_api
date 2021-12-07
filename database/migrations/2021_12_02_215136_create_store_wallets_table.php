<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_wallets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->string('previous_row_hash',1000)->nullable();
            $table->double('amount');
            $table->bigInteger('sender_id');
            $table->string('sender_type',50);
            $table->bigInteger('ledger_type'); // credit = 1, debit = 0
            $table->string('transaction_type',50)->nullable(); //order, transfer, bonus, sub_order
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
        Schema::dropIfExists('store_wallets');
    }
}
