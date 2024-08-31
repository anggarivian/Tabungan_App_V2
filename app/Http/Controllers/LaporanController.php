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
        $user = User::where('roles_id', 4)->paginate(10);
        return view('bendahara.laporan.lap_tabungan', compact('user'));
    }
    public function lap_bendahara_transaksi(Request $request){
        $transaksi = Transaksi::paginate(10);
        return view('bendahara.laporan.lap_transaksi', compact('transaksi'));
    }
    public function lap_bendahara_pengajuan(Request $request){
        $pengajuan = Pengajuan::paginate(10);
        return view('bendahara.laporan.lap_pengajuan', compact('pengajuan'));
    }
}
