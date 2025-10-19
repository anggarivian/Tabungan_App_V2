<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
                'status' => 1,
                'created_at' => Carbon::create(2025, 8, 1, 8, 0, 0),
                // 'updated_at' => Carbon::create(2025, 8, 1, 8, 0, 0),
            ]
        ];

        foreach($bukus as $buku){
            Buku::create($buku);
        }
    }
}
