<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tabungan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data awal pengguna
        $users = [
            [
                'name' => 'isKepsek',
                'username' => 'isKepsek',
                'jenis_kelamin' => 'L',
                'kontak' => '085712341234',
                'alamat' => 'Cianjur',
                'email' => 'kepsek@mail.com',
                'password' => Hash::make('12345'),
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
                'password' => Hash::make('12345'),
                'kelas_id' => 1,
                'roles_id' => 2
            ],
            [
                'name' => 'isWalikelas',
                'username' => 'isWalikelas',
                'jenis_kelamin' => 'L',
                'kontak' => '085712341234',
                'alamat' => 'Cianjur',
                'email' => 'walikelas@mail.com',
                'password' => Hash::make('12345'),
                'kelas_id' => 1,
                'roles_id' => 3
            ],
            [
                'name' => 'isSiswa',
                'username' => 'isSiswa',
                'jenis_kelamin' => 'L',
                'kontak' => '085712341234',
                'alamat' => 'Cianjur',
                'email' => 'siswa@mail.com',
                'password' => Hash::make('12345'),
                'kelas_id' => 1,
                'roles_id' => 4
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Buat tabungan untuk setiap user
            if ($user->roles_id === 4) { // Hanya untuk siswa
                Tabungan::create([
                    'saldo' => 0,
                    'premi' => 0,
                    'sisa' => 0,
                    'user_id' => $user->id
                ]);
            }
        }

        // Faker untuk membuat data siswa acak
        $faker = Faker::create('id_ID');

        // Generate 60 siswa, 10 siswa untuk setiap kelas
        foreach (range(1, 6) as $kelas_id) {
            foreach (range(1, 10) as $i) {
                $user = User::create([
                    'name' => $faker->firstName,
                    'username' => $faker->unique()->numerify('1000##'),
                    'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                    'kontak' => $faker->unique()->phoneNumber,
                    'orang_tua' => $faker->name,
                    'alamat' => $faker->city,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('12345'),
                    'kelas_id' => $kelas_id,
                    'roles_id' => 4
                ]);

                // Buat tabungan untuk siswa
                Tabungan::create([
                    'saldo' => 0,
                    'premi' => 0,
                    'sisa' => 0,
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
