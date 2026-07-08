<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\Kelas;
use App\Models\User;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RombelController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $perPage   = $request->input('perPage', 10);
        $periodeId = PeriodeHelper::id();

        $rombels = Rombel::with(['kelas', 'walikelas'])
            ->withCount('siswa')
            ->where('periode_id', $periodeId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('walikelas', fn ($qq) => $qq->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('kelas', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('kelas_id')
            ->paginate($perPage)
            ->withQueryString();

        $list_kelas     = Kelas::orderBy('name')->get();
        $list_walikelas = User::where('roles_id', 3)->orderBy('name')->get();

        return view('bendahara.rombel.index', compact('rombels', 'list_kelas', 'list_walikelas'));
    }

    public function store(Request $request)
    {
        $periodeId = PeriodeHelper::id();

        if (!$periodeId) {
            return back()->withErrors(['periode' => 'Tidak ada periode aktif. Aktifkan periode terlebih dahulu.']);
        }

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('rombels')->where(fn ($q) => $q->where('periode_id', $periodeId)),
            ],
            'kelas_id'     => 'required|exists:kelas,id',
            'walikelas_id' => 'nullable|exists:users,id',
        ], [
            'name.unique' => 'Nama rombel ini sudah dipakai di periode aktif.',
        ]);

        Rombel::create([
            'name'         => $request->name,
            'kelas_id'     => $request->kelas_id,
            'walikelas_id' => $request->walikelas_id,
            'periode_id'   => $periodeId,
        ]);

        return redirect()->back()->with([
            'success' => true,
            'alert-type' => 'success',
            'alert-message' => 'Rombel berhasil ditambahkan!',
            'alert-duration' => 3000,
        ]);
    }

    public function getStudents($id)
    {
        $periodeId = PeriodeHelper::id();
        $rombel = Rombel::where('periode_id', $periodeId)->findOrFail($id);

        $anggota = $rombel->siswa()
            ->where('roles_id', 4)
            ->wherePivot('periode_id', $periodeId)   // <-- defensif, biar gak ambil pivot row lain
            ->orderBy('name')
            ->get();

        $belum_rombel = User::where('roles_id', 4)
            ->where('status_siswa', 'aktif')
            ->whereDoesntHave('rombel', function ($q) use ($periodeId) {
                $q->where('rombel_user.periode_id', $periodeId); // fix ambigu dari sebelumnya
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'rombel'       => $rombel,
            'anggota'      => $anggota,
            'belum_rombel' => $belum_rombel,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function update(Request $request, $id)
    {
        $periodeId = PeriodeHelper::id();
        $rombel = Rombel::where('periode_id', $periodeId)->findOrFail($id);

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('rombels')->where(fn ($q) => $q->where('periode_id', $periodeId))->ignore($rombel->id),
            ],
            'kelas_id'      => 'required|exists:kelas,id',
            'walikelas_id'  => 'nullable|exists:users,id',
            'student_ids'   => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $studentIds = array_map('intval', $request->input('student_ids', []));

        // guard konflik siswa di rombel lain -- tetep sama kayak sebelumnya
        if (!empty($studentIds)) {
            $konflik = User::whereIn('id', $studentIds)
                ->whereHas('rombel', function ($q) use ($periodeId, $rombel) {
                    $q->where('rombels.periode_id', $periodeId)
                    ->where('rombels.id', '!=', $rombel->id);
                })
                ->pluck('name');

            if ($konflik->isNotEmpty()) {
                return back()->withErrors([
                    'student_ids' => 'Siswa berikut sudah terdaftar di rombel lain pada periode ini: ' . $konflik->implode(', '),
                ]);
            }
        }

        // simpan anggota LAMA sebelum sync -- buat nentuin siapa yang "baru masuk"
        $anggotaLama = $rombel->siswa()->pluck('users.id')->map(fn ($id) => (int) $id)->all();

        $rombel->update([
            'name'         => $request->name,
            'kelas_id'     => $request->kelas_id,
            'walikelas_id' => $request->walikelas_id,
        ]);

        $syncData = [];
        foreach ($studentIds as $sid) {
            $syncData[$sid] = ['periode_id' => $periodeId];
        }

        try {
            $rombel->siswa()->sync($syncData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors([
                'student_ids' => 'Terjadi konflik data (kemungkinan ada siswa yang baru saja ditambahkan ke rombel lain). Silakan muat ulang halaman dan coba lagi.',
            ]);
        }

        // assign kode CUMA buat yang baru masuk -- anggota lama gak disentuh sama sekali
        $anggotaBaru = array_values(array_diff($studentIds, $anggotaLama));
        $rombel->assignKodeAnggotaBaru($anggotaBaru);

        return redirect()->back()->with([
            'success' => true,
            'alert-type' => 'success',
            'alert-message' => 'Rombel dan Anggota berhasil diperbarui!',
            'alert-duration' => 3000,
        ]);
    }

    public function destroy($id)
    {
        $periodeAktifId = PeriodeHelper::id();
        $rombel = Rombel::findOrFail($id);

        if ($rombel->periode_id !== $periodeAktifId) {
            return redirect()->back()->with([
                'success' => false,
                'alert-type' => 'danger',
                'alert-message' => 'Rombel dari periode yang sudah lewat tidak bisa dihapus karena merupakan data riwayat kelas siswa.',
                'alert-duration' => 4000,
            ]);
        }

        $rombel->delete();

        return redirect()->back()->with([
            'success' => true,
            'alert-type' => 'success',
            'alert-message' => 'Rombel berhasil dihapus!',
            'alert-duration' => 3000,
        ]);
    }
}