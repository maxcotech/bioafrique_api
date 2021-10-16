<?php

use App\Traits\HasUserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    use HasUserStatus;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('store_name');
            $table->string('store_logo')->nullable();
            $table->integer('country_id');
            $table->string('store_slug')->unique();
            $table->string('store_address')->nullable();
            $table->string('store_email')->nullable();
            $table->string('store_telephone')->nullable();
            $table->tinyInteger('store_status')->default($this->getActiveUserId());
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
        Schema::dropIfExists('stores');
    }
}
