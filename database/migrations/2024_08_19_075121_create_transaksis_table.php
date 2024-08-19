<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah_transaksi');
            $table->integer('saldo_awal');
            $table->integer('saldo_akhir');
            $table->string('tipe_transaksi');
            $table->string('pembayaran');
            $table->string('pembuat');
            $table->string('token_stor');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tabungan_id')->constrained('tabungans');
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
        Schema::dropIfExists('transaksis');
    }
}
