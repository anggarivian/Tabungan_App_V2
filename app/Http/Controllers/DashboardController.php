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

    public function index(Request $request)
    {
        // 1. User yang paling sering transaksi (stor & success)
        $palingSeringData = Transaksi::select('user_id', DB::raw('COUNT(*) as total_transaksi'))
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->groupBy('user_id')
            ->orderByDesc('total_transaksi')
            ->with('user') // relasi user() di model Transaksi
            ->first();

        if ($palingSeringData && $palingSeringData->user) {
            $palingSering = (object)[
                'nama'  => $palingSeringData->user->name,
                'value' => $palingSeringData->total_transaksi,
            ];
        } else {
            $palingSering = (object)[ 'nama' => null, 'value' => 0 ];
        }

        // 2. User dengan saldo tabungan terbesar (khusus roles_id = 4)
        $palingGedeData = Tabungan::select('tabungans.user_id', DB::raw('CAST(tabungans.saldo AS UNSIGNED) as saldo_numeric'))
            ->join('users', 'users.id', '=', 'tabungans.user_id')
            ->where('users.roles_id', 4)
            ->orderByDesc('saldo_numeric')
            ->with('user') // relasi user() di model Tabungan
            ->first();

        if ($palingGedeData && $palingGedeData->user) {
            $palingGede = (object)[
                'nama'  => $palingGedeData->user->name,
                'value' => $palingGedeData->saldo_numeric,
            ];
        } else {
            $palingGede = (object)[ 'nama' => null, 'value' => 0 ];
        }

        return view('welcome', compact('palingSering', 'palingGede'));
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

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->count();
        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->count();

        return view('kepsek.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'storKali',
            'tarikKali'
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

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->count();
        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->count();

        return view('bendahara.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'premi',
            'bendahara',
            'storKali',
            'tarikKali'
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

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->whereHas('user.kelas', function ($query) use ($kelas_id) {
                $query->where('id', $kelas_id);
            })
            ->count();

        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->whereHas('user.kelas', function ($query) use ($kelas_id) {
                $query->where('id', $kelas_id);
            })
            ->count();

        return view('walikelas.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'storKali',
            'tarikKali'
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

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->where('user_id', $userId)
            ->count();

        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->where('user_id', $userId)
            ->count();

        return view('siswa.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'transaksi',
            'storKali',
            'tarikKali'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }
}
