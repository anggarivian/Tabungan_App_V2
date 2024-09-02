<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function kelola_pengajuan()
    {
        $pengajuan= Pengajuan::paginate(10);

        return view('bendahara.kelola_pengajuan', compact('pengajuan'));
    }
}
