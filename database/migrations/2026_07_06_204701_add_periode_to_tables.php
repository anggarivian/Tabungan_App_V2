<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeriodeToTables extends Migration
{
    public function up()
    {
        Schema::table('rombels', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('kelas_id')
                ->constrained('periodes')->nullOnDelete();
            $table->foreign('walikelas_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('tabungans', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('user_id')
                ->constrained('periodes')->nullOnDelete();
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('user_id')
                ->constrained('periodes')->nullOnDelete();
        });

        Schema::table('pengajuans', function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->after('user_id')
                ->constrained('periodes')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rombels', function (Blueprint $table) {
            $table->dropForeign(['walikelas_id']);
            $table->dropConstrainedForeignId('periode_id');
        });
        Schema::table('tabungans', fn (Blueprint $t) => $t->dropConstrainedForeignId('periode_id'));
        Schema::table('transaksis', fn (Blueprint $t) => $t->dropConstrainedForeignId('periode_id'));
        Schema::table('pengajuans', fn (Blueprint $t) => $t->dropConstrainedForeignId('periode_id'));
    }
}