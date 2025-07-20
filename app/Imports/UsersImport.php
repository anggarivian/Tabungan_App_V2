<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Tabungan;  // Pastikan model Tabungan diimport
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $kelasMap = [
        '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6 , '7' => 7,
    ];

    public function model(array $row)
    {
        $kelasId = $this->kelasMap[$row['kelas']] ?? null;

        if (!$kelasId) {
            return null;
        }

        // Generate username sesuai aturan
        $existingUser = User::where('username', 'like', $row['kelas'] . '__') // dua digit setelah kelas
                    ->orderBy('username', 'desc')
                    ->first();
        if ($existingUser) {
            $lastDigits = substr($existingUser->username, strlen($row['kelas']));
            $nextNumber = (int)$lastDigits + 1;
            $username = $row['kelas'] . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        } else {
            $username = $row['kelas'] . '01';
        }

        // Generate email berdasarkan username
        $email = $username . '@mail.com';

        // Buat user baru
        $user = new User([
            'name'          => $row['name'],
            'username'      => $username,
            'email'         => $email,
            'password'      => Hash::make('12345'),
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'kontak'        => $row['kontak'] ?? null,
            'orang_tua'     => $row['orang_tua'] ?? null,
            'alamat'        => $row['alamat'] ?? null,
            'kelas_id'      => $kelasId,
            'roles_id'      => 4,
        ]);

        // Simpan user
        $user->save();

        // Setelah user disimpan, buat entri di model Tabungan
        $tabungan = new Tabungan();
        $tabungan->saldo = 0;
        $tabungan->premi = 0;
        $tabungan->sisa = 0;
        $tabungan->user_id = $user->id;  // Mengambil ID user yang baru saja disimpan
        $tabungan->save();

        return $user;
    }
}
