<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tabungan;
use App\Models\Transaksi;

class TabunganController extends Controller
{
    /**
     * Menampilkan halaman index tabungan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('bendahara.tabungan.index');
    }

    /**
     * Menampilkan halaman stor tabungan.
     *
     * @return \Illuminate\View\View
     */
    public function stor()
    {
        return view('bendahara.tabungan.stor');
    }

    /**
     * Menampilkan halaman tarik tabungan.
     *
     * @return \Illuminate\View\View
     */
    public function tarik()
    {
        return view('bendahara.tabungan.tarik');
    }

    /**
     * Mencari data siswa berdasarkan username dan mengembalikan detail tabungan.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $username = $request->get('username');

        $user = User::with('tabungan')->where('username', $username)->first();

        if ($user) {
            return response()->json([
                'name' => $user->name,
                'kelas' => $user->kelas->name,
                'tabungan' => $user->tabungan->saldo,
            ]);
        } else {
            return response()->json([
                'name' => 'Tidak Ada',
                'kelas' => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
            ]);
        }
    }

    /**
     * Menyimpan data stor tabungan ke dalam basis data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storTabungan(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_tabungan' => 'required|numeric',
            'jumlah_stor' => 'required|numeric',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric' => 'Jumlah tabungan harus berupa angka.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric' => 'Jumlah stor harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_stor'];
        $transaksi->saldo_awal = $validatedData['jumlah_tabungan'];
        $transaksi->saldo_akhir = $validatedData['jumlah_tabungan'] + $validatedData['jumlah_stor'] ;
        $transaksi->tipe_transaksi = 'Stor';
        $transaksi->pembayaran = 'Offline';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id ;
        $transaksi->tabungan_id = $user->tabungan->id;

        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $tabungan->saldo = $transaksi->saldo_akhir ;
        $tabungan->premi = $transaksi->saldo_akhir / 100 * 2.5 ;
        $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi ;

        $transaksi->save();

        $tabungan->save();

        return redirect()->back()->with('success', 'Tabungan berhasil disimpan')->with('alert-type', 'success')->with('alert-message', 'Tabungan berhasil disimpan')->with('alert-duration', 3000);
    }

/**
     * Menarik uang dari tabungan dan menyimpan data ke dalam basis data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tarikTabungan(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_tabungan' => 'required|numeric',
            'jumlah_tarik' => 'required|numeric',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric' => 'Jumlah tabungan harus berupa angka.',
            'jumlah_tarik.required' => 'Jumlah tarik harus diisi.',
            'jumlah_tarik.numeric' => 'Jumlah tarik harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_tarik'];
        $transaksi->saldo_awal = $validatedData['jumlah_tabungan'];
        $transaksi->saldo_akhir = $validatedData['jumlah_tabungan'] - $validatedData['jumlah_tarik'];
        $transaksi->tipe_transaksi = 'Tarik';
        $transaksi->pembayaran = 'Offline';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id;
        $transaksi->tabungan_id = $user->tabungan->id;

        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $tabungan->saldo = $transaksi->saldo_akhir;
        $tabungan->premi = $transaksi->saldo_akhir / 100 * 2.5;
        $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi;

        $transaksi->save();

        $tabungan->save();

        return redirect()->back()->with('success', 'Tabungan berhasil ditarik')->with('alert-type', 'success')->with('alert-message', 'Tabungan berhasil ditarik')->with('alert-duration', 3000);
    }
}
