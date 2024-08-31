<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Tabungan;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function index()
    {
        $user= User::with(['tabungan', 'transaksi', 'pengajuan'])
        ->where('roles_id', 4)
        ->paginate(10);

        dd($user);
        return view('bendahara.kelola_pengajuan', compact('user'));
    }
}
