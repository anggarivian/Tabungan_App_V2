<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeriodeToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            Schema::table('tabungans', function (Blueprint $table) {
                $table->foreignId('periode_id')->after('user_id');
            });

            Schema::table('transaksis', function (Blueprint $table) {
                $table->foreignId('periode_id')->after('user_id');
            });

            Schema::table('pengajuans', function (Blueprint $table) {
                $table->foreignId('periode_id')->after('user_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
}
