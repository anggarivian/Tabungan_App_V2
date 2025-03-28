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
            $table->string('pembayaran'); // Tunai/Digital
            $table->string('pembuat');
            $table->string('checkout_link')->nullable(); // URL dari Xendit
            $table->string('external_id')->nullable(); // ID unik dari sistem untuk Xendit
            $table->string('status')->default('pending'); // Status pembayaran
            $table->string('token_stor')->nullable(); // Token transaksi
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
