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

    public function kepsek(){
        $jumlah_penabung = User::where('roles_id', 4)->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');
        $jumlah_saldo = Tabungan::sum('saldo');

        // Data untuk chart
        $data_masuk = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Stor')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $data_keluar = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Tarik')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Menyiapkan data dalam array berdasarkan bulan
        $chart_masuk = array_fill(0, 12, 0);
        $chart_keluar = array_fill(0, 12, 0);

        foreach ($data_masuk as $item) {
            $chart_masuk[$item->bulan - 1] = $item->total;
        }

        foreach ($data_keluar as $item) {
            $chart_keluar[$item->bulan - 1] = $item->total;
        }

        return view('kepsek.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_masuk' => $chart_masuk,
            'chart_keluar' => $chart_keluar,
        ]);
    }

    public function bendahara(){
        $jumlah_penabung = User::where('roles_id', 4)->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');
        $jumlah_saldo = Tabungan::sum('saldo');

        // Data untuk chart
        $data_masuk = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Stor')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $data_keluar = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Tarik')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Menyiapkan data dalam array berdasarkan bulan
        $chart_masuk = array_fill(0, 12, 0);
        $chart_keluar = array_fill(0, 12, 0);

        foreach ($data_masuk as $item) {
            $chart_masuk[$item->bulan - 1] = $item->total;
        }

        foreach ($data_keluar as $item) {
            $chart_keluar[$item->bulan - 1] = $item->total;
        }

        return view('bendahara.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_masuk' => $chart_masuk,
            'chart_keluar' => $chart_keluar,
        ]);
    }

    public function walikelas(){
        $kelas_id = auth()->user()->kelas_id; // Mendapatkan kelas_id pengguna yang login

        $jumlah_penabung = User::where('roles_id', 4)
            ->where('kelas_id', $kelas_id) // Filter berdasarkan kelas_id
            ->count();

        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id); // Filter berdasarkan kelas_id
            })
            ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id); // Filter berdasarkan kelas_id
            })
            ->sum('jumlah_transaksi');

        $jumlah_saldo = Tabungan::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id); // Filter berdasarkan kelas_id
        })->sum('saldo');

        // Data untuk chart
        $data_masuk = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Stor')
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id); // Filter berdasarkan kelas_id
            })
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $data_keluar = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Tarik')
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id); // Filter berdasarkan kelas_id
            })
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Menyiapkan data dalam array berdasarkan bulan
        $chart_masuk = array_fill(0, 12, 0);
        $chart_keluar = array_fill(0, 12, 0);

        foreach ($data_masuk as $item) {
            $chart_masuk[$item->bulan - 1] = $item->total;
        }

        foreach ($data_keluar as $item) {
            $chart_keluar[$item->bulan - 1] = $item->total;
        }

        return view('walikelas.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_masuk' => $chart_masuk,
            'chart_keluar' => $chart_keluar,
        ]);
    }

    public function siswa(){
        // Ambil ID pengguna yang sedang login
        $userId = Auth::id();

        // Hitung jumlah penabung yang sesuai dengan user login
        $jumlah_penabung = User::where('roles_id', 4)
            ->where('id', $userId)
            ->count();

        // Hitung transaksi masuk hanya untuk user login
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('user_id', $userId)
            ->sum('jumlah_transaksi');

        // Hitung transaksi keluar hanya untuk user login
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('user_id', $userId)
            ->sum('jumlah_transaksi');

        // Hitung saldo tabungan hanya untuk user login
        $jumlah_saldo = Tabungan::where('user_id', $userId)
            ->sum('saldo');

        // Data untuk chart transaksi masuk
        $data_masuk = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Stor')
            ->where('user_id', $userId) // Hanya untuk user login
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Data untuk chart transaksi keluar
        $data_keluar = Transaksi::select(
                DB::raw('SUM(jumlah_transaksi) as total'),
                DB::raw('MONTH(created_at) as bulan')
            )
            ->where('tipe_transaksi', 'Tarik')
            ->where('user_id', $userId) // Hanya untuk user login
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Menyiapkan data dalam array berdasarkan bulan
        $chart_masuk = array_fill(0, 12, 0);
        $chart_keluar = array_fill(0, 12, 0);

        foreach ($data_masuk as $item) {
            $chart_masuk[$item->bulan - 1] = $item->total;
        }

        foreach ($data_keluar as $item) {
            $chart_keluar[$item->bulan - 1] = $item->total;
        }

        return view('siswa.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_masuk' => $chart_masuk,
            'chart_keluar' => $chart_keluar,
        ]);
    }
}
