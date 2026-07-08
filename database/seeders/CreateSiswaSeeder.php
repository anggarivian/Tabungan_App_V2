<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rombel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateSiswaSeeder extends Seeder
{
    public function run()
    {
        $rombelKelas1 = Rombel::where('kelas_id', 1)->first();

        for ($i = 1; $i <= 50; $i++) {

            $siswa = User::create([
                'name'            => 'Siswa ' . $i,
                'username'        => 'siswa' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'jenis_kelamin'   => $i % 2 == 0 ? 'P' : 'L',
                'kontak'          => '081234567' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'alamat'          => 'Cianjur',
                'email'           => 'siswa' . $i . '@mail.com',
                'password'        => Hash::make('12345'),
                'roles_id'        => 4,
                'status_siswa'    => 'aktif',
            ]);

            if ($rombelKelas1) {
                $rombelKelas1->siswa()->attach($siswa->id, [
                    'no_absen' => $i,
                ]);
            }
        }
    }
}