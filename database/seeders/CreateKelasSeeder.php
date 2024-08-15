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
        $kelas = [
            [
                'id' => 1,
                'name' => '1 A'
            ],
            [
                'id' => 2,
                'name' => '1 B'
            ],
            [
                'id' => 3,
                'name' => '2 A'
            ],
            [
                'id' => 4,
                'name' => '2 B'
            ],
            [
                'id' => 5,
                'name' => '3 A'
            ],
            [
                'id' => 6,
                'name' => '3 B'
            ],
            [
                'id' => 7,
                'name' => '4'
            ],
            [
                'id' => 8,
                'name' => '5'
            ],
            [
                'id' => 9,
                'name' => '6'
            ]
        ];

        foreach($kelas as $key => $kelas){
            Kelas::create($kelas);
        }
    }
}
