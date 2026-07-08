<?php

namespace Database\Seeders;

use App\Models\Rombel;
use App\Models\User;
use App\Models\Periode;
use Illuminate\Database\Seeder;

class CreateRombelSeeder extends Seeder
{
    public function run()
    {
        $periodeAktif = Periode::where('status', true)->first();

        // mapping: username walikelas => kelas yang dia pegang
        $mapping = [
            'niamarliana'   => 1,
            'rikamayasari'  => 2,
            'mardialamsyah' => 3,
            'ainurhayati'   => 4,
            'rohmayati'     => 5,
            'tresnakomala'  => 6,
        ];

        foreach ($mapping as $username => $kelasId) {
            $walikelas = User::where('username', $username)->first();

            Rombel::create([
                'name'         => "Kelas {$kelasId}",
                'walikelas_id' => $walikelas?->id,
                'kelas_id'     => $kelasId,
                'periode_id'   => $periodeAktif->id,
            ]);
        }
    }
}