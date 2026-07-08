<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Periode;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $rombels = Rombel::with(['kelas', 'walikelas'])
            ->withCount('users')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('walikelas', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('kelas', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->paginate($perPage);

        $list_kelas = Kelas::all();
        $list_walikelas = User::where('roles_id', 3)->get(); // Role 3 = Walikelas

        return view('bendahara.rombel.index', compact('rombels', 'list_kelas', 'list_walikelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'walikelas_id' => 'nullable|exists:users,id',
        ]);

        $active_periode = Periode::where('status', 1)->first();

        Rombel::create([
            'name' => $request->name,
            'kelas_id' => $request->kelas_id,
            'walikelas_id' => $request->walikelas_id,
            'periode_id' => $active_periode ? $active_periode->id : null,
        ]);

        return redirect()->back()->with([
            'success' => true,
            'alert-type' => 'success',
            'alert-message' => 'Rombel berhasil ditambahkan!',
            'alert-duration' => 3000
        ]);
    }

    public function getStudents($id)
    {
        $rombel = Rombel::findOrFail($id);
        $active_periode = Periode::where('status', 1)->first();
        $active_periode_id = $active_periode ? $active_periode->id : null;

        // Anggota rombel ini
        $anggota = $rombel->users()->where('roles_id', 4)->get();

        // Siswa aktif yang belum masuk rombel manapun di periode aktif saat ini
        $belum_rombel = User::where('roles_id', 4)
            ->where('status_siswa', 'aktif')
            ->whereDoesHave('rombels', function ($q) use ($active_periode_id) {
                $q->where('periode_id', $active_periode_id);
            })
            ->get();

        return response()->json([
            'rombel' => $rombel,
            'anggota' => $anggota,
            'belum_rombel' => $belum_rombel
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'walikelas_id' => 'nullable|exists:users,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $rombel = Rombel::findOrFail($id);
        $rombel->update([
            'name' => $request->name,
            'kelas_id' => $request->kelas_id,
            'walikelas_id' => $request->walikelas_id,
        ]);

        // Sinkronisasi anggota siswa di tabel pivot (rombel_user)
        $studentIds = $request->input('student_ids', []);
        $rombel->users()->sync($studentIds);

        return redirect()->back()->with([
            'success' => true,
            'alert-type' => 'success',
            'alert-message' => 'Rombel dan Anggota berhasil diperbarui!',
            'alert-duration' => 3000
        ]);
    }

    public function destroy($id)
    {
        $rombel = Rombel::findOrFail($id);
        $rombel->delete();

        return redirect()->back()->with([
            'success' => true,
            'alert-type' => 'success',
            'alert-message' => 'Rombel berhasil dihapus!',
            'alert-duration' => 3000
        ]);
    }
}