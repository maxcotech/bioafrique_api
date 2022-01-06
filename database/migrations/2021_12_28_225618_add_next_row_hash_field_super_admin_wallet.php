<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNextRowHashFieldSuperAdminWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('super_admin_wallet', function (Blueprint $table) {
            $table->string('next_row_hash',500)->after('previous_row_hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('super_admin_wallet', function (Blueprint $table) {
            $table->dropColumn(['next_row_hash']);
        });
    }
}
