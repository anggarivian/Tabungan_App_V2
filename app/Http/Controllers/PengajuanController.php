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
    public function kelola_pengajuan(Request $request)
    {
        $perPage = request('perPage', 10);

        $query = Pengajuan::query()->where('status', 'Pending');
        $query->select('id', 'jumlah_penarikan', 'status', 'pembayaran', 'alasan', 'user_id', 'tabungan_id');
        $searchTerm = $request->input('search');

        if (!empty($searchTerm)) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('jumlah_penarikan', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('status', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('pembayaran', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('alasan', 'LIKE', '%'.$searchTerm.'%');
            });

            $query->orWhereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%'.$searchTerm.'%');
            });

            $query->orWhereHas('user.kelas', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        $query->orderBy('created_at', 'desc');
        $pengajuan = $query->paginate($perPage);

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
            'jumlah_tarik' => $pengajuan->jumlah_penarikan ?? '-',
            'pembayaran' => $pengajuan->pembayaran ?? '-',
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

    public function terima(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required',
            'kelas' => 'required|string|max:255',
            'tabungan' => 'required|numeric',
            'jumlah_tarik' => 'required',
            'pembayaran' => 'required|string|max:255',
            'alasan' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tarik.required' => 'Jumlah penarikan harus diisi.',
            'pembayaran.required' => 'Pembayaran harus diisi.',
            'alasan.required' => 'Alasan harus diisi.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();

        $pengajuan = pengajuan::where('user_id', $user->id)->firstOrFail();

        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $pengajuan->status = 'Terima' ;

        if ($validatedData['jumlah_tarik'] > $tabungan->saldo) {
            return redirect()->back()->withErrors(['jumlah_tarik' => 'Penarikan tabungan melebihi saldo tabungan'])->withInput();
        }

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_tarik'];
        $transaksi->saldo_awal = $tabungan->saldo;
        $transaksi->saldo_akhir = $tabungan->saldo - $validatedData['jumlah_tarik'];
        $transaksi->tipe_transaksi = 'Tarik';
        $transaksi->pembayaran = 'Tunai';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id;
        $transaksi->tabungan_id = $tabungan->id;

        $tabungan->saldo = $transaksi->saldo_akhir;
        $tabungan->premi = $tabungan->saldo / 100 * 2.5;
        $tabungan->sisa = $tabungan->saldo - $tabungan->premi;

        $transaksi->save();
        $tabungan->save();
        $pengajuan->save();

        return redirect()->route('bendahara.pengajuan.index')->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Terima.')->with('alert-type', 'success')->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Terima.')->with('alert-duration', 30000);
    }

    public function tolak($id)
    {
        $pengajuan = Pengajuan::where('user_id', $id)->latest()->first();

        $pengajuan->status = 'Tolak';

        $pengajuan->save();

        return redirect()->route('bendahara.pengajuan.index')->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Tolak.')->with('alert-type', 'danger')->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Tolak.')->with('alert-duration', 30000);
    }

}
