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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('kontak')->nullable();
            $table->string('password');
            $table->string('orang_tua')->nullable();
            $table->string('alamat')->nullable();
            $table->string('tahun_ajaran')->nullable();
            $table->foreignId('buku_id')
                ->nullable()
                ->constrained('bukus')
                ->nullOnDelete();
            $table->foreignId('kelas_id')
                ->nullable()
                ->constrained('kelas')
                ->nullOnDelete();;
            $table->foreignId('roles_id')
                ->nullable()
                ->constrained('roles')
                ->nullOnDelete();;;
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
