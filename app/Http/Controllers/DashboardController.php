<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tabungan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * DashboardController menghandle tampilan dashboard untuk berbagai role.
 */
class DashboardController extends Controller
{
    // Kepsek Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Kepala Sekolah.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function index(Request $request){

        // 1. User yang paling sering transaksi

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

        // 2. User yang saldo akhirnya paling besar (diambil dari model Tabungan::saldo)

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

        // 3. User yang paling konsisten “streak” menabung setiap minggu (kecuali hari Minggu)

        $allUsers = User::with(['transaksi' => function($q){
            $q->whereRaw('DAYOFWEEK(created_at) != 1')
            ->orderBy('created_at');
        }])->get();

        $maxStreakGlobal = 0;
        $palingKonsisten = (object)[ 'nama' => null, 'value' => 0 ];

        foreach ($allUsers as $user) {
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
                    $currentStreak = 1;
                } else {
                    $yesterday = $prevDate->copy()->addDay();
                    $skipSunday = $prevDate->dayOfWeek == Carbon::SATURDAY
                                && $curr->dayOfWeek == Carbon::MONDAY;

                    if ($curr->toDateString() === $yesterday->toDateString() || $skipSunday) {
                        $currentStreak++;
                    } else {
                        $currentStreak = 1;
                    }
                }

                $prevDate = $curr->copy();
                if ($currentStreak > $streakMaxUser) {
                    $streakMaxUser = $currentStreak;
                }
            }

            if ($streakMaxUser > $maxStreakGlobal) {
                $maxStreakGlobal = $streakMaxUser;
                $palingKonsisten = (object)[
                    'nama'  => $user->name,
                    'value' => $streakMaxUser,
                ];
            }
        }

        if ($maxStreakGlobal === 0) {
            $palingKonsisten = (object)[ 'nama' => null, 'value' => 0 ];
        }

        return view('welcome', compact('palingSering', 'palingGede', 'palingKonsisten'));
    }

    // Kepsek Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Kepala Sekolah.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function kepsek(){
        $jumlah_penabung = User::where('roles_id', 4)->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->sum('jumlah_transaksi');
        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        // Chart -----------------------------------------------------------------------------------------------
        $frekuensi = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        $total = Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "stor" THEN jumlah_transaksi WHEN tipe_transaksi = "tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        return view('kepsek.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }

    // Bendahara Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara(){
        $jumlah_penabung = User::where('roles_id', 4)->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->sum('jumlah_transaksi');
        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');
        $premi = Tabungan::sum('premi');
        $bendahara = Tabungan::where('user_id', 2)->first();

        // Chart -----------------------------------------------------------------------------------------------
        $frekuensi = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        $total = Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "stor" THEN jumlah_transaksi WHEN tipe_transaksi = "tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        return view('bendahara.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'premi',
            'bendahara'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }

    // Walikelas Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Walikelas.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas(){
        $kelas_id = auth()->user()->kelas_id;

        $jumlah_penabung = User::where('roles_id', 4)
            ->where('kelas_id', $kelas_id)
            ->count();

        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id);
            })
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id);
            })
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        $jumlah_saldo = Tabungan::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->sum('saldo');

        // Chart -----------------------------------------------------------------------------------------------
        $walikelas = auth()->user();

        $jumlah_penabung = User::where('roles_id', 4)
            ->where('kelas_id', $walikelas->kelas_id)
            ->count();

        $transaksi_masuk = Transaksi::whereHas('user', function($query) use ($walikelas) {
                    $query->where('kelas_id', $walikelas->kelas_id);
                })
                ->where('tipe_transaksi', 'Stor')
                ->where('status', 'success')
                ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::whereHas('user', function($query) use ($walikelas) {
                    $query->where('kelas_id', $walikelas->kelas_id);
                })
                ->where('tipe_transaksi', 'Tarik')
                ->where('status', 'success')
                ->sum('jumlah_transaksi');

        $jumlah_saldo_tunai = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                })->where('tipe_transaksi', 'Stor')
                ->where('status', 'success')
                ->where('pembayaran', 'Tunai')
                ->sum('jumlah_transaksi')
                - Transaksi::whereHas('user', function ($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                })->where('tipe_transaksi', 'Tarik')
                ->where('status', 'success')
                ->where('pembayaran', 'Tunai')
                ->sum('jumlah_transaksi');

        $jumlah_saldo_digital = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                })->where('tipe_transaksi', 'Stor')
                ->where('status', 'success')
                ->where('pembayaran', 'Digital')
                ->sum('jumlah_transaksi')
                - Transaksi::whereHas('user', function ($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                })->where('tipe_transaksi', 'Tarik')
                ->where('status', 'success')
                ->where('pembayaran', 'Digital')
                ->sum('jumlah_transaksi');

        $frekuensi = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
                ->whereHas('user', function($query) use ($walikelas) {
                    $query->where('kelas_id', $walikelas->kelas_id);
                })
                ->where('tipe_transaksi', 'stor')
                ->where('status', 'success')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'x' => $item->date,
                        'y' => $item->total
                    ];
                });

        $total = Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "stor" THEN jumlah_transaksi WHEN tipe_transaksi = "tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
                ->whereHas('user', function($query) use ($walikelas) {
                    $query->where('kelas_id', $walikelas->kelas_id);
                })
                ->where('status', 'success')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->reduce(function ($carry, $item) {
                    $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                    $carry->push([
                        'x' => $item->date,
                        'y' => (string) ($lastTotal + $item->daily_total)
                    ]);
                    return $carry;
                }, collect());

        return view('walikelas.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }

    // Siswa Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Siswa.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa(){
        $userId = Auth::id();

        $jumlah_penabung = User::where('roles_id', 4)
            ->where('id', $userId)
            ->count();

        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->where('user_id', $userId)->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->where('user_id', $userId)->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->where('user_id', $userId)->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->where('user_id', $userId)->sum('jumlah_transaksi');

        // Chart -----------------------------------------------------------------------------------------------
        $siswa = auth()->user();

        $jumlah_penabung = User::where('roles_id', 4)
            ->where('id', $siswa->id)
            ->count();

        $transaksi_masuk = Transaksi::where('user_id', $siswa->id)
                ->where('tipe_transaksi', 'Stor')
                ->where('status', 'success')
                ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::where('user_id', $siswa->id)
                ->where('tipe_transaksi', 'Tarik')
                ->where('status', 'success')
                ->sum('jumlah_transaksi');

        $jumlah_saldo = Tabungan::where('user_id', $siswa->id)
                ->sum('saldo');

        $frekuensi = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('user_id', $siswa->id)
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        $total = Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "stor" THEN jumlah_transaksi WHEN tipe_transaksi = "tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('user_id', $siswa->id)
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        $perPage = request('perPage', 10);

        $transaksi = Transaksi::query()
            ->where('status', 'success')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('siswa.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'transaksi'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }
}
