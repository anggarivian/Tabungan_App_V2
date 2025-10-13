<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tabungan;
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
        $initialUsers = [
            [
                'name' => 'isKepsek',
                'username' => 'isKepsek',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'kepsek@mail.com',
                'password' => $defaultPassword,
                'kelas_id' => null,
                'roles_id' => 1
            ],
            [
                'name' => 'Operator Bendahara',
                'username' => 'admin',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'email' => 'bendahara@mail.com',
                'password' => $defaultPassword,
                'kelas_id' => null,
                'roles_id' => 2
            ],
            [
                'name' => 'Nia Marliana',
                'username' => 'niamarliana',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'niamarliana08@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'rombel_id' => 1,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Rika Mayasari',
                'username' => 'rikamayasari',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'rikamayasari43264@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 2,
                'rombel_id' => 2,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Mardi Alamsyah',
                'username' => 'mardialamsyah',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'mardialamsyah43264@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 3,
                'rombel_id' => 3,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Ai Nurhayati',
                'username' => 'ainurhayati',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'ainurhayati61@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 4,
                'rombel_id' => 4,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Rohmayati',
                'username' => 'rohmayati',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'rohmayati039@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 5,
                'rombel_id' => 5,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Tresna Komala',
                'username' => 'tresnakomala',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'ktresna82@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 6,
                'rombel_id' => 6,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Dede Suhendi',
                'username' => 'dedesuhendi',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'dedesuhendi01@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 1,
                'rombel_id' => 7,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Lina Rosdiana',
                'username' => 'linarosdiana',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'linarosdiana02@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 2,
                'rombel_id' => 8,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Asep Saepuloh',
                'username' => 'asepsaepuloh',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'asepsaepuloh03@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 3,
                'rombel_id' => 9,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Euis Nuraeni',
                'username' => 'euisnuraeni',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'euisnuraeni04@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 4,
                'rombel_id' => 10,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Roni Setiawan',
                'username' => 'ronisetiawan',
                'jenis_kelamin' => 'L',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'ronisetiawan05@gmail.com',
                'password' => $defaultPassword,
                'kelas_id' => 5,
                'rombel_id' => 11,
                'roles_id' => 3,
                'buku_id' => 1
            ],
            [
                'name' => 'Yani Herlina',
                'username' => 'yaniherlina',
                'jenis_kelamin' => 'P',
                'kontak' => '1234567890',
                'alamat' => 'Cianjur',
                'tahun_ajaran' => '2025',
                'email' => 'yaniherlina06@guru.sd.belajar.id',
                'password' => $defaultPassword,
                'kelas_id' => 6,
                'rombel_id' => 12,
                'roles_id' => 3,
                'buku_id' => 1
            ]
        ];

        foreach ($initialUsers as $userData) {
            User::create($userData);
        }

        $extraNames = [
            'Andi Saputra', 'Budi Santoso', 'Citra Lestari', 'Dewi Anggraini',
            'Eko Prasetyo', 'Fajar Ramadhan', 'Gina Marlina', 'Hendra Gunawan',
            'Indah Permata', 'Joko Widodo', 'Kartika Sari', 'Lukman Hakim'
        ];

        $users = [];
        $index = 0;

        // Buku aktif (bisa sesuaikan atau ambil dari DB)
        $bukuId = 1; // atau Buku::where('status', 1)->first()->id ?? 1;

        for ($kelasId = 1; $kelasId <= 6; $kelasId++) {
            for ($i = 0; $i < 2; $i++) {
                $name = $extraNames[$index] ?? "Siswa {$kelasId}-{$i}";
                $username = strtolower(str_replace(' ', '', $name));

                // Buat user siswa
                $user = User::create([
                    'name' => $name,
                    'username' => "{$kelasId}" . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'email' => "{$username}@siswa.sd.belajar.id",
                    'password' => $defaultPassword,
                    'jenis_kelamin' => (rand(0, 1) ? 'L' : 'P'),
                    'kontak' => '08' . rand(100000000, 999999999),
                    'alamat' => 'Cianjur',
                    'orang_tua' => 'Orang Tua ' . $name,
                    'tahun_ajaran' => '2025',
                    'kelas_id' => $kelasId,
                    'buku_id' => $bukuId,
                    'roles_id' => 4
                ]);

                // Buat tabungan untuk siswa ini
                Tabungan::create([
                    'saldo' => 0,
                    'premi' => 0,
                    'sisa' => 0,
                    'buku_id' => $bukuId,
                    'user_id' => $user->id
                ]);

                $index++;
            }
        }
    }
}
