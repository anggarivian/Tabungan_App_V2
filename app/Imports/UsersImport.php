<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Tabungan;
use App\Helpers\PeriodeHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    protected ?int $periodeId;

    public function __construct()
    {
        $this->periodeId = PeriodeHelper::id();
    }

    /**
     * Validasi tiap baris sebelum diproses.
     * Baris yang gagal validasi otomatis di-skip (gak bikin proses berhenti total)
     * dan bisa ditampilkan lewat $import->failures().
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P,l,p',
            'orang_tua'     => 'nullable|string|max:255',
            'alamat'        => 'nullable|string',
        ];
    }

    public function model(array $row)
    {
        // kalau gak ada periode aktif, semua baris gak bisa diproses --
        // mending gagal jelas daripada tabungan ke-create tanpa periode
        if (!$this->periodeId) {
            return null;
        }

        if (empty($row['name'])) {
            return null;
        }

        $username = $this->generateUsername($row['name']);
        $email = $username . '@mail.com';

        // wrap dalam transaction: kalau Tabungan gagal dibuat, User ikut di-rollback
        // biar gak ada siswa "yatim" tanpa tabungan
        return DB::transaction(function () use ($row, $username, $email) {
            $user = User::create([
                'name'          => trim($row['name']),
                'username'      => $username,
                'email'         => $email,
                'password'      => Hash::make('12345'),
                'jenis_kelamin' => !empty($row['jenis_kelamin']) ? strtoupper(trim($row['jenis_kelamin'])) : null,
                'kontak'        => isset($row['kontak']) ? (string) $row['kontak'] : null,
                'orang_tua'     => $row['orang_tua'] ?? null,
                'alamat'        => $row['alamat'] ?? null,
                'status_siswa'  => 'aktif',
                'roles_id'      => 4,
            ]);

            Tabungan::create([
                'saldo'      => 0,
                'premi'      => 0,
                'sisa'       => 0,
                'user_id'    => $user->id,
                'periode_id' => $this->periodeId,
            ]);

            return $user;
        });
    }

    /**
     * Sama persis dengan logic di SiswaController::add(),
     * biar username konsisten mau daftar manual atau lewat import.
     */
    private function generateUsername(string $nama): string
    {
        $namaRaw = strtolower(trim($nama));
        $namaClean = preg_replace('/[^a-z\s]/', '', $namaRaw);
        $words = array_values(array_filter(explode(' ', $namaClean)));

        $count = count($words);
        $username = '';

        if ($count == 1) {
            $username = $words[0] . $words[0];
        } elseif ($count == 2) {
            $username = $words[0] . $words[1];
        } elseif ($count >= 3) {
            $username = $words[0] . $words[1];
            for ($i = 2; $i < $count; $i++) {
                $username .= substr($words[$i], 0, 1);
            }
        }

        if (empty($username)) {
            $username = 'siswa';
        }

        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}