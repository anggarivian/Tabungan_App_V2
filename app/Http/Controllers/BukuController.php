<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kelas;
use App\Models\Tabungan;
use App\Models\Rombel;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BukuController extends Controller
{
    /**
     * Show list of buku.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $buku = Buku::orderByDesc('tahun')->paginate($perPage);

        return view('bendahara.kelola_buku', compact('buku'));
    }

    /**
     * Add new buku.
     */
    public function add(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1)
        ]);

        // Check if there’s already an open book
        $existingActive = Buku::where('status', 1)->first();
        if ($existingActive) {
            return redirect()
                ->route('bendahara.pembukuan.index')
                ->with('alert-type', 'warning')
                ->with('alert-message', 'Masih ada buku yang aktif. Tutup terlebih dahulu sebelum menambah baru.')
                ->with('alert-duration', 3000);
        }

        // Check duplicate tahun
        if (Buku::where('tahun', $request->tahun)->exists()) {
                return redirect()
                    ->route('bendahara.pembukuan.index')
                    ->with('alert-type', 'warning')
                    ->with('alert-message', 'Buku untuk tahun ini sudah ada.')
                    ->with('alert-duration', 3000);
        }

        // Create new Buku
        $buku = Buku::create([
            'tahun' => $request->tahun,
            'status' => 1
        ]);

        // Create 6 Kelas linked to this Buku
        for ($i = 1; $i <= 6; $i++) {
            Kelas::create([
                'name' => (string)$i,
                'buku_id' => $buku->id
            ]);
        }

        // --- Generate "Naik Kelas" if checked ---
        if ($request->has('generate_naik_kelas') && $request->generate_naik_kelas == 1) {
            $tahunSebelumnya = $request->tahun - 1;

            // Find previous year book
            $bukuLama = Buku::where('tahun', $tahunSebelumnya)->first();
            if ($bukuLama) {
                // Fetch all siswa from last year
                $siswaLama = User::where('roles_id', 4)
                    ->where('buku_id', $bukuLama->id)
                    ->get();

                $kelasMap = [1, 2, 3, 4, 5, 6, 7];

                foreach ($siswaLama as $siswa) {

                    if ($siswa->kelas_id >= 6) continue;

                    $kelasBaru = Kelas::where('buku_id', $buku->id)
                        ->where('name', (string)($siswa->kelas_id + 1))
                        ->first();

                    if (!$kelasBaru) continue;

                    $kelasIndex = (int)$kelasBaru->name;
                    $existingUser = User::where('username', 'like', $kelasMap[$kelasIndex - 1] . '%')
                        ->orderBy('username', 'desc')
                        ->first();

                    if ($existingUser) {
                        $nextNumber = (int)substr($existingUser->username, -2) + 1;
                        $newUsername = $kelasMap[$kelasIndex - 1] . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
                    } else {
                        $newUsername = $kelasMap[$kelasIndex - 1] . '01';
                    }
                    
                    $newUser = $siswa->replicate(['id']); // 
                    $newUser->username = $newUsername;
                    $newUser->buku_id = $buku->id;
                    $newUser->kelas_id = $kelasBaru->id;
                    $newUser->tahun_ajaran = $buku->tahun;
                    $newUser->save();

                    Tabungan::create([
                        'saldo' => 0,
                        'premi' => 0,
                        'sisa' => 0,
                        'buku_id' => $buku->id,
                        'user_id' => $newUser->id
                    ]);
                }
            }
        }

        return redirect()
            ->route('bendahara.pembukuan.index')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Buku baru berhasil ditambahkan' . 
                ($request->generate_naik_kelas ? ' dan siswa telah digenerate naik kelas.' : '.'))
            ->with('alert-duration', 3000);
    }



    /**
     * Edit tahun buku (only if still active)
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1)
        ]);

        $buku = Buku::findOrFail($id);

        if ($buku->status == 0) {
            return redirect()
                ->route('bendahara.pembukuan.index')
                ->with('alert-type', 'warning')
                ->with('alert-message', 'Buku yang sudah ditutup tidak dapat diedit.')
                ->with('alert-duration', 3000);
        }

        // Prevent duplicate tahun
        if (Buku::where('tahun', $request->tahun)->where('id', '!=', $id)->exists()) {
            return redirect()
                ->route('bendahara.pembukuan.index')
                ->with('alert-type', 'warning')
                ->with('alert-message', 'Tahun yang dimasukkan sudah digunakan oleh buku lain.')
                ->with('alert-duration', 3000);
        }

        $buku->update([
            'tahun' => $request->tahun
        ]);

        return redirect()
            ->route('bendahara.pembukuan.index')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Tahun buku berhasil diperbarui.')
            ->with('alert-duration', 3000);
    }


    /**
     * Tutup buku (once closed cannot reopen)
     */
    public function tutup($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->status == 0) {
            return redirect()
                ->route('bendahara.pembukuan.index')
                ->with('alert-type', 'warning')
                ->with('alert-message', 'Buku ini sudah ditutup sebelumnya.')
                ->with('alert-duration', 3000);
        }

        $buku->update(['status' => 0]);

        return redirect()
            ->route('bendahara.pembukuan.index')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Buku berhasil ditutup.')
            ->with('alert-duration', 3000);
    }

    public function detail($id, Request $request)
    {
        $buku = Buku::findOrFail($id);
        $selectedKelas = $request->get('kelas_id');

        $kelasList = Kelas::where('buku_id', $buku->id)->orderBy('name')->get();

        $rombels = Rombel::with(['kelas', 'walikelas'])
            ->whereHas('kelas', fn($q) => $q->where('buku_id', $buku->id))
            ->withCount(['users as total_siswa' => function ($q) use ($buku) {
                $q->where('roles_id', 4)->where('buku_id', $buku->id);
            }])
            ->orderBy('kelas_id')
            ->get();

        $walikelas = User::with(['kelas', 'rombel'])
            ->where('roles_id', 3)
            ->where('buku_id', $buku->id)
            ->get();

        $siswaQuery = User::with(['kelas', 'rombel'])
            ->where('roles_id', 4)
            ->where('buku_id', $buku->id);

        if ($selectedKelas) {
            $siswaQuery->where('kelas_id', $selectedKelas);
        }

        $siswa = $siswaQuery->get();

        $tabungans = Kelas::where('buku_id', $buku->id)
            ->with(['users.tabungan' => function ($q) use ($buku) {
                $q->where('buku_id', $buku->id);
            }])
            ->orderBy('name')
            ->get();

        return view('bendahara.pembukuan.detail', compact(
            'buku',
            'rombels',
            'walikelas',
            'siswa',
            'kelasList',
            'selectedKelas',
            'tabungans',
        ));
    }

    public function filterSiswa($id, Request $request)
    {
        $buku = Buku::findOrFail($id);
        $kelasId = $request->get('kelas_id');

        $query = User::with(['kelas', 'rombel'])
            ->where('roles_id', 4)
            ->where('buku_id', $buku->id);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $siswa = $query->get();

        // Return partial view (only table rows)
        return response()->json([
            'html' => view('bendahara.pembukuan.partials._siswa_table_body', compact('siswa'))->render()
        ]);
    }

    public function rekapTahunan($id)
    {
        $buku = Buku::findOrFail($id);

        // Ambil periode dari waktu buku dibuat dan terakhir diupdate
        $periodeStart = Carbon::parse($buku->created_at)->translatedFormat('d F Y');
        $periodeEnd = Carbon::parse($buku->updated_at)->translatedFormat('d F Y');

        // Total siswa
        $totalSiswa = User::where('roles_id', 4)->where('buku_id', $buku->id)->count();

        // Total saldo keseluruhan
        $totalSaldo = Tabungan::where('buku_id', $buku->id)->sum('saldo');

        // Total masuk & keluar dari transaksi
        $totalMasuk = Transaksi::whereHas('tabungan', function($q) use($buku){
                            $q->where('buku_id', $buku->id);
                        })
                        ->where('tipe_transaksi', 'masuk')
                        ->sum('jumlah_transaksi');

        $totalKeluar = Transaksi::whereHas('tabungan', function($q) use($buku){
                            $q->where('buku_id', $buku->id);
                        })
                        ->where('tipe_transaksi', 'keluar')
                        ->sum('jumlah_transaksi');

        // Breakdown per kelas
        $kelasData = Kelas::where('buku_id', $buku->id)
            ->with(['users.tabungan'])
            ->get()
            ->map(function($kelas){
                $totalSaldo = $kelas->users->sum(fn($u) => $u->tabungan->saldo ?? 0);
                $jumlahSiswa = $kelas->users->count();
                $rataRata = $jumlahSiswa > 0 ? round($totalSaldo / $jumlahSiswa) : 0;
                return [
                    'kelas' => $kelas->name,
                    'jumlah_siswa' => $jumlahSiswa,
                    'total_saldo' => $totalSaldo,
                    'rata_rata' => $rataRata,
                ];
            });

        // Generate PDF
        $pdf = Pdf::loadView('bendahara.pembukuan.pdf.rekap_tahunan', [
            'buku' => $buku,
            'periodeStart' => $periodeStart,
            'periodeEnd' => $periodeEnd,
            'totalSiswa' => $totalSiswa,
            'totalSaldo' => $totalSaldo,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'kelasData' => $kelasData,
            'pembuat' => auth()->user()->name ?? 'Operator Sekolah'
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Rekap-Tahun-' . $buku->tahun . '.pdf');
    }

}