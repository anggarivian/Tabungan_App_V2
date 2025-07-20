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
                'name' => '1'
            ],
            [
                'id' => 2,
                'name' => '2'
            ],
            [
                'id' => 3,
                'name' => '3'
            ],
            [
                'id' => 4,
                'name' => '4'
            ],
            [
                'id' => 5,
                'name' => '5'
            ],
            [
                'id' => 6,
                'name' => '6'
            ],
            [
                'id' => 7,
                'name' => '7'
            ]
        ];

        foreach($kelas as $key => $kelas){
            Kelas::create($kelas);
        }
    }
}
