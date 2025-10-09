<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class CreateBukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bukus = [
            [
                'id' => 1,
                'tahun' => '2025',
                'status' => 1
            ]
        ];

        foreach($bukus as $buku){
            Buku::create($buku);
        }
    }
}
