<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RombelController extends Controller
{
    public function index(Request $request)
    {
        $tahunSekarang = date('Y');

        $bukuExsist = Buku::where('tahun', $tahunSekarang)->where('status', 1)->first();

        if (!$bukuExsist) {
            $bukuExsist = Buku::where('status', 1)->first();
        }

        if (!$bukuExsist) {
            return redirect()
                    ->route('bendahara.pembukuan.index')
                    ->with('alert-type', 'warning')
                    ->with('alert-message', 'Tidak ada pembukuan yang tersedia')
                    ->with('alert-duration', 3000);
        } else {
            $rombels = Rombel::with(['kelas', 'walikelas'])
                ->whereHas('kelas', function ($q) use ($bukuExsist) {
                    $q->where('buku_id', $bukuExsist->id);
                })
                ->withCount(['users as total_siswa' => function ($q) use ($bukuExsist) {
                    $q->where('roles_id', 4)
                    ->where('buku_id', $bukuExsist->id);
                }])
                ->orderBy('kelas_id')
                ->get();
        }

        $perPage = $request->get('perPage', 10);
        $buku = Buku::orderByDesc('tahun')->paginate($perPage);
        
        $kelas = Kelas::where('buku_id', $bukuExsist->id)->get();
        $walikelas = User::where('roles_id', 3)->where('buku_id', $bukuExsist->id)->get();

        return view('bendahara.rombel.index', compact('rombels', 'kelas', 'walikelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string',
        'kelas_id' => 'required|exists:kelas,id',
        'walikelas_id' => 'nullable|exists:users,id'
        ]);

        $rombel = Rombel::create([
            'name' => $request->name,
            'kelas_id' => $request->kelas_id,
        ]);

        if ($request->filled('walikelas_id')) {
            User::where('id', $request->walikelas_id)
                ->update(['rombel_id' => $rombel->id]);
        }

        return redirect()->back()
            ->with('alert-type', 'success')
            ->with('alert-message', 'Rombel berhasil ditambahkan!')
            ->with('alert-duration', 3000);
    }

    public function show($id)
    {
        $rombel = Rombel::with(['siswa', 'kelas', 'walikelas'])->findOrFail($id);

        $belumRombel = User::where('kelas_id',  $rombel->kelas_id)
            ->get();

        $belumRombel = User::where('kelas_id',  $rombel->kelas_id)
            ->where('roles_id', 4)
            ->whereNull('rombel_id')
            ->get();

        return response()->json([
            'rombel' => [
                'id' => $rombel->id,
                'name' => $rombel->name,
                'kelas' => $rombel->kelas ?? '-',
                'walikelas' => $rombel->walikelas ?? '-'
            ],
            'anggota' => $rombel->siswa->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name
            ]),
            'belumRombel' => $belumRombel
        ]);
    }

    public function updateAnggota(Request $request, $id)
    {
        $rombel = Rombel::findOrFail($id);

        DB::transaction(function () use ($request, $rombel) {
            User::where('rombel_id', $rombel->id)->where('roles_id', 4)->update(['rombel_id' => null]);

            if ($request->filled('anggota')) {
                User::whereIn('id', $request->anggota)->update(['rombel_id' => $rombel->id]);
            }
        });

        return response()->json(['success' => true]);
    }


    public function destroy($id)
    {
        $rombel = Rombel::findOrFail($id);

        User::where('rombel_id', $rombel->id)->update(['rombel_id' => null]);

        $rombel->delete();

        return redirect()->back()
            ->with('alert-type', 'success')
            ->with('alert-message', 'Rombel berhasil dihapus!')
            ->with('alert-duration', 3000);
    }
}