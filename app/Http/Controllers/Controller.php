<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Tabungan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        //
    // 1. User yang paling sering transaksi
    //
    // Hitung jumlah transaksi per user dan dapatkan yang terbanyak.
    $palingSeringData = Transaksi::select('user_id', DB::raw('COUNT(*) as total_transaksi'))
        ->groupBy('user_id')
        ->orderByDesc('total_transaksi')
        ->first();

    if ($palingSeringData) {
        $palingSering = (object)[
            'nama'  => User::find($palingSeringData->user_id)->name,
            'value' => $palingSeringData->total_transaksi,
        ];
    } else {
        $palingSering = (object)[
            'nama'  => null,
            'value' => 0,
        ];
    }

    //
    // 2. User yang saldo akhirnya paling besar (diambil dari model Tabungan::saldo)
    //
    // Karena kolom `saldo` di tabel tabungans bertipe string, kita cast ke unsigned integer terlebih dahulu.
    $palingGedeData = Tabungan::select('user_id', DB::raw('CAST(saldo AS UNSIGNED) as saldo_numeric'))
        ->orderByDesc('saldo_numeric')
        ->first();

    if ($palingGedeData) {
        $userGede = User::find($palingGedeData->user_id);
        $palingGede = (object)[
            'nama'  => $userGede ? $userGede->name : null,
            'value' => $palingGedeData->saldo_numeric,
        ];
    } else {
        $palingGede = (object)[
            'nama'  => null,
            'value' => 0,
        ];
    }

    //
    // 3. User yang paling konsisten “streak” menabung setiap minggu (kecuali hari Minggu)
    //
    // Definisi “streak” di sini: hitung streak hari berturut-turut di mana user melakukan transaksi,
    // dengan aturan bahwa gap hari Minggu (Sunday) tidak memutus streak. Misalnya:
    // Senin → Selasa → Rabu, atau Jumat → Sabtu → Senin (Sabtu ke Senin diloloskan karena Minggu di-skip).
    //
    // Algoritma:
    //  - Ambil semua user beserta daftar tanggal (DATE(created_at)) transaksi mereka,
    //    filter keluar yang jatuh pada hari Minggu (DAYOFWEEK != 1).
    //  - Urutkan tanggal, unique, lalu hitung streak terpanjang berdasarkan aturan di atas.
    //  - Pilih user dengan nilai streak tertinggi.
    //
    // NOTE: Jika data sangat besar, sebaiknya optimasi atau hitung offline. Contoh di bawah untuk kebutuhan demo saja.

    $allUsers = User::with(['transaksi' => function($q){
        // Ambil hanya transaksi yang bukan hari Minggu, ordered by tanggal naik
        $q->whereRaw('DAYOFWEEK(created_at) != 1')
          ->orderBy('created_at');
    }])->get();

    $maxStreakGlobal = 0;
    $palingKonsisten = (object)[ 'nama' => null, 'value' => 0 ];

    foreach ($allUsers as $user) {
        // Ambil daftar tanggal unik (YYYY-MM-DD) dari setiap transaksi yang sudah difilter
        $dateStrings = $user->transaksi
            ->map(function($t){ return Carbon::parse($t->created_at)->toDateString(); })
            ->unique()
            ->sort()
            ->values();

        $streakMaxUser = 0;
        $currentStreak = 0;
        $prevDate = null;

        foreach ($dateStrings as $dateStr) {
            $curr = Carbon::parse($dateStr);

            if (is_null($prevDate)) {
                // Awal streak
                $currentStreak = 1;
            } else {
                // Hitung apakah curr hari berikutnya dari prevDate,
                // atau prevDate hari Sabtu dan curr hari Senin (skip Minggu)
                $yesterday = $prevDate->copy()->addDay();              // prevDate +1 hari
                $skipSunday = $prevDate->dayOfWeek == Carbon::SATURDAY   // Sabtu → Minggu → Senin
                            && $curr->dayOfWeek == Carbon::MONDAY;

                if ($curr->toDateString() === $yesterday->toDateString() || $skipSunday) {
                    // Masih berurutan → tambah streak
                    $currentStreak++;
                } else {
                    // Streak terputus → reset
                    $currentStreak = 1;
                }
            }

            // Update prevDate dan cek max per user
            $prevDate = $curr->copy();
            if ($currentStreak > $streakMaxUser) {
                $streakMaxUser = $currentStreak;
            }
        }

        // Jika streak user > streak global, simpan sebagai calon paling konsisten
        if ($streakMaxUser > $maxStreakGlobal) {
            $maxStreakGlobal = $streakMaxUser;
            $palingKonsisten = (object)[
                'nama'  => $user->name,
                'value' => $streakMaxUser,
            ];
        }
    }

    // Jika tidak ada transaksi sama sekali, tetap berikan default 0/null
    if ($maxStreakGlobal === 0) {
        $palingKonsisten = (object)[ 'nama' => null, 'value' => 0 ];
    }

    //
    // Kembalikan ke view (atau response JSON) dengan tiga object:
    // $palingSering, $palingGede, $palingKonsisten
    //
    return view('welcome', compact('palingSering', 'palingGede', 'palingKonsisten'));
    }
}
