<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kelas;
use App\Models\Tabungan;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function detail($id)
    {
        $buku = Buku::findOrFail($id);

        // Load all related data filtered by buku_id
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

        $siswa = User::with(['kelas', 'rombel'])
            ->where('roles_id', 4)
            ->where('buku_id', $buku->id)
            ->get();

        return view('bendahara.pembukuan.detail', compact('buku', 'rombels', 'walikelas', 'siswa'));
    }

}