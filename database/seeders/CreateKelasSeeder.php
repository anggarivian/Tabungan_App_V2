<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class CreateKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kelas = [];
        $id = 1;

        for ($i = 1; $i <= 6; $i++) {
            // A class
            $kelas[] = [
                'id' => $id++,
                'name' => (string)$i,
                'rombel' => 'A',
                'buku_id' => 1
            ];

            // B class
            $kelas[] = [
                'id' => $id++,
                'name' => (string)$i,
                'rombel' => 'B',
                'buku_id' => 1
            ];
        }

        foreach($kelas as $key => $kelas){
            Kelas::create($kelas);
        }
    }
}
