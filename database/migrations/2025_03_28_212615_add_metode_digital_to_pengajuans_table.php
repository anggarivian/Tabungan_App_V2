<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetodeDigitalToPengajuansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            // Tambahan kolom untuk detail pembayaran digital
            $table->string('metode_digital')->nullable()->after('pembayaran');
            $table->string('bank_code')->nullable()->after('metode_digital');
            $table->string('nomor_rekening')->nullable()->after('bank_code');
            $table->string('ewallet_type')->nullable()->after('nomor_rekening');
            $table->string('ewallet_number')->nullable()->after('ewallet_type');

            // Kolom untuk Xendit Payout
            $table->string('xendit_payout_id')->nullable()->after('ewallet_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn([
                'metode_digital',
                'bank_code',
                'nomor_rekening',
                'ewallet_type',
                'ewallet_number',
                'xendit_payout_id'
            ]);
        });
    }
}
