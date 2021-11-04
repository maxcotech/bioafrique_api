<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //auth_type 0 for form auth, 1 for facebook, 2 for google
        //user_type 1 for customers,10 for store staffs,
        // 24 for super owner
        //account_status 1 = enabled , 2 = disabled
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->string('telephone_code')->nullable();
            $table->tinyInteger('auth_type')->default(0);
            $table->string('email')->unique();
            $table->tinyInteger('user_type')->default(1);
            $table->tinyInteger('account_status')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
