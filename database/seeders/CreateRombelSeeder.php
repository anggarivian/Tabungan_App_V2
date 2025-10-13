<?php

namespace Database\Seeders;

use App\Models\Rombel;
use Illuminate\Database\Seeder;

class CreateRombelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rombels = [];

        for ($i = 1; $i <= 6; $i++) {
            $rombels[] = [
                'name' => 'A',
                'kelas_id' => $i
            ];
        }
        for ($i = 1; $i <= 6; $i++) {
            $rombels[] = [
                'name' => 'B',
                'kelas_id' => $i
            ];
        }

        foreach ($rombels as $rombel) {
            Rombel::create($rombel);
        }

    }
}
