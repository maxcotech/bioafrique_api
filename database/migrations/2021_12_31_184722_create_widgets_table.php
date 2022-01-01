<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('widget_title',1000)->nullable();
            $table->string('widget_link_address',1000)->nullable();
            $table->string('widget_link_text',1000)->nullable();
            $table->tinyInteger('widget_type');
            $table->integer('index_no')->unique();
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
        Schema::dropIfExists('widgets');
    }
}
