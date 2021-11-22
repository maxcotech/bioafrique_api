<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->string('group_name');
            $table->double('shipping_rate');
            $table->integer('delivery_duration');
            $table->double('door_delivery_rate')->nullable();
            $table->double('high_value_rate')->nullable();
            $table->double('mid_value_rate')->nullable();
            $table->double('low_value_rate')->nullable();
            $table->json('dimension_range_rates')->nullable();
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
        Schema::dropIfExists('shipping_groups');
    }
}
