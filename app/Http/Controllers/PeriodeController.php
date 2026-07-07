<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Periode;
use App\Models\Tabungan;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::latest()->get();

        return view(
            'bendahara.periode.index',
            compact('periodes')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'=>'required|unique:periodes',
            'tanggal_mulai'=>'required',
            'tanggal_selesai'=>'required'
        ]);

        $periode = Periode::create([
            'nama'=>$request->nama,
            'tanggal_mulai'=>$request->tanggal_mulai,
            'tanggal_selesai'=>$request->tanggal_selesai,
            'status'=>0
        ]);

        $siswas = User::where('roles_id',4)->get();

        foreach($siswas as $siswa){

            Tabungan::create([
                'user_id'=>$siswa->id,
                'periode_id'=>$periode->id,
                'saldo'=>0,
                'premi'=>0,
                'sisa'=>0
            ]);

        }

        return back()
        ->with('success','Periode berhasil dibuat.');
    }

    public function aktifkan(Periode $periode)
    {
        Periode::query()->update([
            'status'=>0
        ]);

        $periode->update([
            'status'=>1
        ]);

        return back()
        ->with('success','Periode berhasil diaktifkan.');
    }
}