<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRombelUserTable extends Migration
{
    public function up()
    {
        Schema::create('rombel_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_id')->constrained('rombels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->string('kode', 20)->nullable();
            $table->timestamps();

            $table->unique(['rombel_id', 'user_id']);
            // jaminan DB-level: 1 siswa cuma boleh 1 rombel dalam 1 periode
            $table->unique(['user_id', 'periode_id']);
            $table->unique(['rombel_id', 'kode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rombel_user');
    }
}