<?php

namespace Database\Seeders;

use App\Models\Tabungan;
use App\Models\User;
use App\Models\Periode;
use Illuminate\Database\Seeder;

class CreateTabunganSeeder extends Seeder
{
    public function run()
    {
        $bendahara = User::where('username', 'admin')->first();
        $periodeAktif = Periode::where('status', true)->first();

        if (!$bendahara || !$periodeAktif) {
            $this->command->warn('User bendahara atau periode aktif tidak ditemukan, skip seeding tabungan admin.');
            return;
        }

        Tabungan::create([
            'saldo' => 0,
            'premi' => 0,
            'sisa' => 0,
            'user_id' => $bendahara->id,
            'periode_id' => $periodeAktif->id,
        ]);
    }
}