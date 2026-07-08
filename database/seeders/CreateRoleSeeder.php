<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class CreateRoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'Kepala Sekolah'],
            ['id' => 2, 'name' => 'Bendahara'],
            ['id' => 3, 'name' => 'Walikelas'],
            ['id' => 4, 'name' => 'Siswa'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}