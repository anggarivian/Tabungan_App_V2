<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rombel extends Model
{
    protected $fillable = ['name', 'walikelas_id', 'kelas_id', 'periode_id'];

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'rombel_user', 'rombel_id', 'user_id')
                    ->withPivot('kode');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'rombel_user', 'rombel_id', 'user_id')
                    ->withPivot('kode')
                    ->withTimestamps();
    }

    public function walikelas()
    {
        return $this->belongsTo(User::class, 'walikelas_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    /**
     * Ambil inti dari nama rombel bebas-format jadi kode singkat.
     * "Rombel 1A" / "1A" / "Kelas 1A" / "Kelas1A" -> semua jadi "1A"
     */
    public static function extractKode(string $namaRombel): string
    {
        $kataUmum = ['kelas', 'rombel', 'rombongan', 'belajar', 'kls', 'rmb'];

        $clean = strtolower(trim($namaRombel));

        foreach ($kataUmum as $kata) {
            $clean = str_ireplace($kata, '', $clean);
        }

        $clean = preg_replace('/[^a-z0-9]/', '', $clean);

        return $clean !== '' ? strtoupper($clean) : 'X'; // fallback kalau nama gak sisa apa-apa
    }

    /**
     * Hitung ulang kode semua anggota rombel ini, urut abjad nama, 01-99.
     * Dipanggil setiap kali anggota rombel berubah (sync).
     */
    public function recomputeKode(): void
    {
        $kodeDasar = self::extractKode($this->name);

        DB::transaction(function () use ($kodeDasar) {
            // clear dulu, supaya gak tabrakan unique constraint pas re-assign urutan baru
            DB::table('rombel_user')->where('rombel_id', $this->id)->update(['kode' => null]);

            $siswaIds = $this->siswa()->orderBy('users.name')->pluck('users.id');

            $urutan = 1;
            foreach ($siswaIds as $userId) {
                $kode = $kodeDasar . str_pad($urutan, 2, '0', STR_PAD_LEFT);

                DB::table('rombel_user')
                    ->where('rombel_id', $this->id)
                    ->where('user_id', $userId)
                    ->update(['kode' => $kode]);

                $urutan++;
            }
        });
    }

    /**
     * Assign kode HANYA untuk anggota baru, tanpa nyentuh kode anggota lama.
     * Nomor lanjut dari nomor tertinggi yang masih aktif dipakai di rombel ini.
     * Anggota baru diurutkan berdasarkan user_id (bukan abjad).
     */
    public function assignKodeAnggotaBaru(array $userIdsBaru): void
    {
        if (empty($userIdsBaru)) {
            return;
        }

        $kodeDasar = self::extractKode($this->name);

        DB::transaction(function () use ($userIdsBaru, $kodeDasar) {
            // cari nomor tertinggi yang MASIH aktif dipakai saat ini
            // (setelah sync, jadi kalau si pemegang nomor tertinggi baru aja keluar,
            // nomornya otomatis gak kehitung lagi di sini)
            $kodeAktif = DB::table('rombel_user')
                ->where('rombel_id', $this->id)
                ->whereNotNull('kode')
                ->pluck('kode');

            $maxNomor = 0;
            foreach ($kodeAktif as $kode) {
                if (preg_match('/(\d+)$/', $kode, $m)) {
                    $maxNomor = max($maxNomor, (int) $m[1]);
                }
            }

            $ids = array_values(array_unique(array_map('intval', $userIdsBaru)));
            sort($ids); // urut berdasarkan id, bukan abjad

            $urutan = $maxNomor + 1;
            foreach ($ids as $userId) {
                $kode = $kodeDasar . str_pad($urutan, 2, '0', STR_PAD_LEFT);

                DB::table('rombel_user')
                    ->where('rombel_id', $this->id)
                    ->where('user_id', $userId)
                    ->update(['kode' => $kode]);

                $urutan++;
            }
        });
    }
}
