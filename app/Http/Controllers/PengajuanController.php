<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Helpers\RupiahHelper;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function kelola_pengajuan()
    {
        $pengajuan= Pengajuan::paginate(10);

        return view('bendahara.kelola_pengajuan', compact('pengajuan'));
    }

    public function getPengajuanData($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        return response()->json([
            'id' => $pengajuan->user->id ?? '-',
            'username' => $pengajuan->user->username ?? '-',
            'name' => $pengajuan->user->name ?? '-',
            'kelas' => $pengajuan->user->kelas->name ?? '-',
            'tabungan' => $pengajuan->user->tabungan->saldo ?? '-',
            'alasan' => $pengajuan->alasan ?? '-',
            'jumlah_penarikan' => $pengajuan->jumlah_penarikan ?? '-',
        ]);
    }

    public function ajukan(Request $request)
    {
        $validatedData = $request->validate([
            'jumlah_tarik' => 'required|numeric|min:1000|max:' . auth()->user()->tabungan->saldo,
            'alasan' => 'required|string|max:255',
            'id' => 'required',
            'jenis_pembayaran' => 'required|in:Tunai,Digital',
        ], [
            'jumlah_tarik.required' => 'Jumlah penarikan harus diisi.',
            'jumlah_tarik.numeric' => 'Jumlah penarikan harus berupa angka.',
            'jumlah_tarik.min' => 'Jumlah penarikan minimal Rp. 1000.',
            'jumlah_tarik.max' => 'Jumlah penarikan tidak boleh melebihi saldo tabungan.',
            'alasan.required' => 'Alasan penarikan harus diisi.',
            'alasan.max' => 'Alasan tidak boleh lebih dari 255 karakter.',
            'jenis_pembayaran.required' => 'Jenis pembayaran harus dipilih.',
            'jenis_pembayaran.in' => 'Jenis pembayaran harus salah satu dari: Tunai, Digital.',
        ]);

        $saldoAwal = auth()->user()->tabungan->saldo;

        // Cek apakah jumlah tarik melebihi saldo awal
        if ($validatedData['jumlah_tarik'] > $saldoAwal) {
            return redirect()->back()->withErrors(['jumlah_tarik' => 'Penarikan tabungan melebihi saldo tabungan.'])->withInput();
        }

        $pengajuan = new Pengajuan();
        $pengajuan->user_id = auth()->user()->id;
        $pengajuan->tabungan_id = auth()->user()->tabungan->id;
        $pengajuan->jumlah_penarikan = $validatedData['jumlah_tarik'];
        $pengajuan->alasan = $validatedData['alasan'];
        $pengajuan->pembayaran = $validatedData['jenis_pembayaran'];
        $pengajuan->status = 'Pending';
        $pengajuan->save();

        return redirect()->route('siswa.tabungan.tarik')->with('success', 'Penarikan tabungan berhasil diajukan.')->with('alert-type', 'success')->with('alert-message', 'Penarikan tabungan berhasil diajukan.')->with('alert-duration', 30000);
    }

}
