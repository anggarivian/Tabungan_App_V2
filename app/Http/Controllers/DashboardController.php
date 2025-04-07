<?php

namespace App\Http\Controllers;

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

    public function kepsek(){
        $jumlah_penabung = User::where('roles_id', 4)->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->sum('jumlah_transaksi');
        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');


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
        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

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
            'jumlah_saldo_digital'
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
                ->sum('jumlah_transaksi');

        $jumlah_saldo_digital = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                })->where('tipe_transaksi', 'Stor')
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

        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->where('user_id', $userId)->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->where('user_id', $userId)->sum('jumlah_transaksi');

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

        return view('siswa.index', compact(
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
}
