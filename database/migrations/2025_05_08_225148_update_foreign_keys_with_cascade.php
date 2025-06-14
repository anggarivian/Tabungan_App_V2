<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysWithCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TABUNGANS
        Schema::table('tabungans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        // TRANSAKSIS
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['tabungan_id']);
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('tabungan_id')
                ->references('id')->on('tabungans')
                ->onDelete('cascade');
        });

        // PENGAJUANS
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['tabungan_id']);
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('tabungan_id')
                ->references('id')->on('tabungans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
