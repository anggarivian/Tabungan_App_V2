<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Password default yang sudah di-hash
        $defaultPassword = Hash::make('12345');

        // Data awal pengguna
        $users = [
            [
                'name' => 'isKepsek',
                'username' => 'isKepsek',
                'jenis_kelamin' => 'L',
                'kontak' => '085712341234',
                'alamat' => 'Cianjur',
                'email' => 'kepsek@mail.com',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'roles_id' => 1
            ],
            [
                'name' => 'isBendahara',
                'username' => 'isBendahara',
                'jenis_kelamin' => 'L',
                'kontak' => '085712341234',
                'alamat' => 'Cianjur',
                'email' => 'bendahara@mail.com',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'roles_id' => 2
            ],
            [
                'name' => 'Nia Marliana',
                'username' => 'niamarliana',
                'jenis_kelamin' => 'P',
                'kontak' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10, Cianjur',
                'email' => 'niamarliana08@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'roles_id' => 3
            ],
            [
                'name' => 'Rika Mayasari',
                'username' => 'rikamayasari',
                'jenis_kelamin' => 'P',
                'kontak' => '081789012345',
                'alamat' => 'Jl. Raya Cianjur No. 50, Cianjur',
                'email' => 'rikamayasari43264@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 2,
                'roles_id' => 3
            ],
            [
                'name' => 'Mardi Alamsyah',
                'username' => 'mardialamsyah',
                'jenis_kelamin' => 'L',
                'kontak' => '081567890123',
                'alamat' => 'Jl. Diponegoro No. 12, Cianjur',
                'email' => 'mardialamsyah43264@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 3,
                'roles_id' => 3
            ],
            [
                'name' => 'Ai Nurhayati',
                'username' => 'ainurhayati',
                'jenis_kelamin' => 'P',
                'kontak' => '081456789012',
                'alamat' => 'Jl. Sudirman No. 5, Cianjur',
                'email' => 'ainurhayati61@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 4,
                'roles_id' => 3
            ],
            [
                'name' => 'Rohmayati',
                'username' => 'rohmayati',
                'jenis_kelamin' => 'P',
                'kontak' => '081890123456',
                'alamat' => 'Jl. HOS Cokroaminoto No. 15, Cianjur',
                'email' => 'rohmayati039@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 5,
                'roles_id' => 3
            ],
            [
                'name' => 'Tresna Komala',
                'username' => 'tresnakomala',
                'jenis_kelamin' => 'P',
                'kontak' => '081678901234',
                'alamat' => 'Jl. Kartini No. 30, Cianjur',
                'email' => 'ktresna82@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 6,
                'roles_id' => 3
            ]
        ];

        // Loop untuk membuat pengguna
        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
