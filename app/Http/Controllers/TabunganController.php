<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
                'kelas' => $user->kelas->name ?? 'Tidak Ada',
                'tabungan' => $user->tabungan->saldo ?? 'Tidak Ada',
            ]);
        } else {
            return response()->json([
                'name' => 'Tidak Ada',
                'kelas' => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
            ]);
        }
    }
}
