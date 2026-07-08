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
            $table->unsignedTinyInteger('no_absen')->nullable();
            $table->timestamps();

            $table->unique(['rombel_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rombel_user');
    }
}