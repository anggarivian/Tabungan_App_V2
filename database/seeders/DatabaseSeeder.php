<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CreateRoleSeeder::class,
            CreateKelasSeeder::class,
            CreatePeriodeSeeder::class,
            CreateUserSeeder::class,
            CreateSiswaSeeder::class,
            CreateRombelSeeder::class,
            CreateTabunganSeeder::class,
        ]);
    }
}
