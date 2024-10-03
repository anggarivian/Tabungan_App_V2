<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Laporan Kepsek ----------------------------------------------------------------------------------
    public function lap_kepsek_tabungan(Request $request){
        $user = User::where('roles_id', 4)->paginate(10);
        return view('kepsek.laporan.lap_tabungan', compact('user'));
    }
    public function lap_kepsek_transaksi(Request $request){
        $transaksi = Transaksi::paginate(10);
        return view('kepsek.laporan.lap_transaksi', compact('transaksi'));
    }
    public function lap_kepsek_pengajuan(Request $request){
        $pengajuan = Pengajuan::paginate(10);
        return view('kepsek.laporan.lap_pengajuan', compact('pengajuan'));
    }

    // Laporan Bendahara --------------------------------------------------------------------------------
    public function lap_bendahara_tabungan(Request $request){
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $sortSaldo = $request->input('sort_saldo');

        $user = User::with(['tabungan', 'kelas'])
            ->whereHas('tabungan')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhereHas('tabungan', function ($q) use ($search) {
                        $q->where('id', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->withSum('tabungan as total_saldo', 'saldo');

        if ($sortSaldo === 'asc' || $sortSaldo === 'desc') {
            $user->orderBy('total_saldo', $sortSaldo);
        }

        $user = $user->paginate(10);

        return view('bendahara.laporan.lap_tabungan', compact('user'));
    }
    public function lap_bendahara_transaksi(Request $request){
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $tipeTransaksi = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');

        $transaksi = Transaksi::with(['user.kelas'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%');
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('user.kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->when($tipeTransaksi, function ($query) use ($tipeTransaksi) {
                $query->where('tipe_transaksi', $tipeTransaksi);
            })
            ->when($tipePembayaran, function ($query) use ($tipePembayaran) {
                $query->where('pembayaran', $tipePembayaran);
            })
            ->paginate(10);

        return view('bendahara.laporan.lap_transaksi', compact('transaksi'));
    }
    public function lap_bendahara_pengajuan(Request $request){
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $status = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');

        $pengajuan = Pengajuan::with(['user', 'user.kelas'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%');
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('user.kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            });

        if ($sortPenarikan === 'asc' || $sortPenarikan === 'desc') {
            $pengajuan->orderBy('jumlah_penarikan', $sortPenarikan);
        }

        $pengajuan = $pengajuan->paginate(10);

        return view('bendahara.laporan.lap_pengajuan', compact('pengajuan'));
    }

    // Laporan Walikelas --------------------------------------------------------------------------------
    public function lap_walikelas_tabungan(Request $request){
        $kelasId = auth()->user()->kelas->id;
        $user = User::where('roles_id', 4)->where('kelas_id', $kelasId)->paginate(10);
        return view('walikelas.laporan.lap_tabungan', compact('user'));
    }
    public function lap_walikelas_transaksi(Request $request){
        $kelasId = auth()->user()->kelas->id;

        $transaksi = Transaksi::whereHas('user.kelas', function ($query) use ($kelasId) {
            $query->where('kelas_id', $kelasId);
        })->paginate(10);
        return view('walikelas.laporan.lap_transaksi', compact('transaksi'));
    }

    // Laporan Walikelas --------------------------------------------------------------------------------
    public function lap_siswa_tabungan(Request $request){
        $kelasId = auth()->user()->kelas->id;
        $user = User::where('roles_id', 4)->where('kelas_id', $kelasId)->paginate(10);
        return view('siswa.laporan.lap_tabungan', compact('user'));
    }
    public function lap_siswa_transaksi(Request $request){
        $kelasId = auth()->user()->kelas->id;

        $transaksi = Transaksi::whereHas('user.kelas', function ($query) use ($kelasId) {
            $query->where('kelas_id', $kelasId);
        })->paginate(10);
        return view('siswa.laporan.lap_transaksi', compact('transaksi'));
    }

    public function lap_siswa_pengajuan(Request $request){
        $pengajuan = Pengajuan::paginate(10);
        return view('siswa.laporan.lap_pengajuan', compact('pengajuan'));
    }
}
