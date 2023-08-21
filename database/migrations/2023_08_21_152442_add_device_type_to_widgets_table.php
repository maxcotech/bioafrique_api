<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceTypeToWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->tinyInteger("device_type")->default(3)->after("widget_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('device_type');
        });
    }
}
