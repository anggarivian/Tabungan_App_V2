<?php

namespace Database\Seeders;

use App\Models\Tabungan;
use Illuminate\Database\Seeder;

class CreateTabunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tabungan::create([
            'id' => 1,
            'saldo' => 0,
            'premi' => 0,
            'sisa' => 0,
            'user_id' => 2,
        ]);
    }
}
