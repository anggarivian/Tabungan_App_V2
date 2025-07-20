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
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'kepsek@mail.com',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'roles_id' => 1
            ],
            [
                'name' => 'Operator Bendahara',
                'username' => 'admin',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'bendahara@mail.com',
                'password' => Hash::make('Agobet1213'),
                'kelas_id' => 1,
                'roles_id' => 2
            ],
            [
                'name' => 'Nia Marliana',
                'username' => 'niamarliana',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'niamarliana08@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'roles_id' => 3
            ],
            [
                'name' => 'Rika Mayasari',
                'username' => 'rikamayasari',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'rikamayasari43264@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 2,
                'roles_id' => 3
            ],
            [
                'name' => 'Mardi Alamsyah',
                'username' => 'mardialamsyah',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'mardialamsyah43264@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 3,
                'roles_id' => 3
            ],
            [
                'name' => 'Ai Nurhayati',
                'username' => 'ainurhayati',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'ainurhayati61@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 4,
                'roles_id' => 3
            ],
            [
                'name' => 'Rohmayati',
                'username' => 'rohmayati',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'rohmayati039@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 5,
                'roles_id' => 3
            ],
            [
                'name' => 'Tresna Komala',
                'username' => 'tresnakomala',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
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
