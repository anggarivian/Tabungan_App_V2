<?php

namespace Database\Seeders;

use App\Models\Periode;
use Illuminate\Database\Seeder;

class CreatePeriodeSeeder extends Seeder
{
    public function run()
    {
        Periode::create([
            'id' => 1,
            'nama' => 'Tahun Ajaran 2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'status' => true,
        ]);
    }
}