<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tabungan;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dan melakukan pencarian + filter.
     */
    public function index(Request $request)
    {
        $perPage      = $request->input('perPage', 10);
        $searchTerm   = $request->input('search');
        $statusFilter = $request->input('status_siswa'); // kosong = semua status
        $jenisKelamin = $request->input('jenis_kelamin');
        $sortBy       = $request->input('sort_by', 'terbaru');

        $query = User::where('roles_id', 4);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('username', 'like', "%{$searchTerm}%")
                  ->orWhere('kontak', 'like', "%{$searchTerm}%")
                  ->orWhere('orang_tua', 'like', "%{$searchTerm}%")
                  ->orWhere('alamat', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status_siswa', $statusFilter);
        }

        if (!empty($jenisKelamin)) {
            $query->where('jenis_kelamin', $jenisKelamin);
        }

        match ($sortBy) {
            'nama_az'  => $query->orderBy('name', 'asc'),
            'nama_za'  => $query->orderBy('name', 'desc'),
            'terlama'  => $query->orderBy('created_at', 'asc'),
            default    => $query->orderBy('created_at', 'desc'), // terbaru
        };

        $user = $query->paginate($perPage)->withQueryString();

        return view('bendahara.kelola_siswa', compact('user'));
    }

    /**
     * Menambahkan data siswa baru + tabungan awal.
     */
    public function add(Request $request)
    {
        $periodeId = PeriodeHelper::id();

        if (!$periodeId) {
            return back()
                ->withErrors(['periode' => 'Tidak ada periode aktif. Hubungi admin untuk mengaktifkan periode terlebih dahulu.'])
                ->withInput();
        }

        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'jenis_kelamin' => 'required|in:L,P',
            'kontak'        => 'required|digits_between:10,15',
            'alamat'        => 'required|string',
            'orang_tua'     => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'kontak.required' => 'Kontak harus diisi.',
            'kontak.digits_between' => 'Kontak harus antara 10 hingga 15 angka.',
            'alamat.required' => 'Alamat harus diisi.',
            'orang_tua.required' => 'Nama orang tua harus diisi.',
        ]);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = bcrypt('12345');
        $user->jenis_kelamin = $validatedData['jenis_kelamin'];
        $user->kontak = $validatedData['kontak'];
        $user->alamat = $validatedData['alamat'];
        $user->orang_tua = $validatedData['orang_tua'];
        $user->status_siswa = 'aktif';
        $user->roles_id = 4;

        // ==========================================
        // LOGIKA GENERATE USERNAME BERDASARKAN NAMA
        // ==========================================
        $namaRaw = strtolower(trim($validatedData['name']));
        $namaClean = preg_replace('/[^a-z\s]/', '', $namaRaw);
        $words = array_values(array_filter(explode(' ', $namaClean)));

        $count = count($words);
        $username = '';

        if ($count == 1) {
            $username = $words[0] . $words[0];
        } elseif ($count == 2) {
            $username = $words[0] . $words[1];
        } elseif ($count >= 3) {
            $username = $words[0] . $words[1];
            for ($i = 2; $i < $count; $i++) {
                $username .= substr($words[$i], 0, 1);
            }
        }

        if (empty($username)) {
            $username = 'siswa'; // fallback kalau nama gak menghasilkan huruf latin sama sekali
        }

        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user->username = $username;
        // ==========================================

        $user->save();

        // Buat data tabungan awal
        $tabungan = new Tabungan();
        $tabungan->saldo = 0;
        $tabungan->premi = 0;
        $tabungan->sisa = 0;
        $tabungan->user_id = $user->id;
        $tabungan->periode_id = $periodeId;
        $tabungan->save();

        return redirect()->route('bendahara.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Siswa baru berhasil dibuat, silakan atur kelasnya di menu Rombel.');
    }

    /**
     * Mengambil data siswa berdasarkan ID (JSON).
     * Fix: scoped ke roles_id=4, cegah akses ke akun non-siswa.
     */
    public function getSiswaData($id)
    {
        $siswa = User::where('roles_id', 4)->findOrFail($id);
        return response()->json($siswa);
    }

    /**
     * Mengedit data siswa + reset password (opsional).
     */
    public function edit(Request $request)
    {
        $user = User::where('roles_id', 4)->findOrFail($request->id); // fix: scoped

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|digits_between:10,15',
            'alamat' => 'required|string',
            'orang_tua' => 'required|string',
            'password' => 'nullable|string|min:8',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'kontak.required' => 'Kontak harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'orang_tua.required' => 'Orang tua harus diisi',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->jenis_kelamin = $validatedData['jenis_kelamin'];
        $user->kontak = $validatedData['kontak'];
        $user->alamat = $validatedData['alamat'];
        $user->orang_tua = $validatedData['orang_tua'];

        if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }

        $user->save();

        return redirect()->route('bendahara.siswa.index')->with([
            'success' => 'Data siswa berhasil diperbarui',
            'alert-type' => 'warning',
            'alert-message' => 'Data siswa berhasil diperbarui'
        ]);
    }

    /**
     * Toggle aktif/nonaktif siswa (bukan hard delete).
     */
    public function toggleStatus($id)
    {
        $siswa = User::where('roles_id', 4)->findOrFail($id);
        $periodeAktifId = PeriodeHelper::id();

        if ($siswa->status_siswa === 'aktif') {
            // AKAN DINONAKTIFKAN: keluarkan dari rombel periode aktif (jika ada) + ubah status, sekaligus
            $rombelSaatIni = $siswa->rombel()
                ->where('rombels.periode_id', $periodeAktifId)
                ->first();

            DB::transaction(function () use ($siswa, $rombelSaatIni, $periodeAktifId) {
                if ($rombelSaatIni) {
                    // hanya lepas keanggotaan di periode AKTIF, riwayat rombel periode lalu tidak disentuh
                    DB::table('rombel_user')
                        ->where('user_id', $siswa->id)
                        ->where('periode_id', $periodeAktifId)
                        ->delete();
                }

                $siswa->status_siswa = 'nonaktif';
                $siswa->save();
            });

            $pesan = $rombelSaatIni
                ? "{$siswa->name} sekarang Nonaktif dan telah dikeluarkan dari rombel {$rombelSaatIni->name}."
                : "{$siswa->name} sekarang berstatus Nonaktif.";

            return redirect()->route('bendahara.siswa.index')
                ->with('success', true)
                ->with('alert-type', 'warning')
                ->with('alert-message', $pesan);
        }

        // AKAN DIAKTIFKAN KEMBALI: cukup balikin status, assign rombel dilakukan manual di menu Rombel
        $siswa->status_siswa = 'aktif';
        $siswa->save();

        return redirect()->route('bendahara.siswa.index')
            ->with('success', true)
            ->with('alert-type', 'success')
            ->with('alert-message', "{$siswa->name} sekarang berstatus Aktif. Silakan assign ke rombel di menu Kelola Rombel.");
    }

    /**
     * Hard delete — hapus siswa beserta seluruh data terkait
     * (tabungan, transaksi, pengajuan ikut terhapus lewat cascade FK).
     */
    public function delete($id)
    {
        $siswa = User::where('roles_id', 4)->findOrFail($id); // fix: scoped
        $nama = $siswa->name;
        $siswa->delete();

        return redirect()->route('bendahara.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus')
            ->with('alert-type', 'danger')
            ->with('alert-message', "Data siswa {$nama} dan seluruh riwayat tabungan/transaksinya berhasil dihapus permanen");
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        if (!\App\Helpers\PeriodeHelper::id()) {
            return back()
                ->withErrors(['file' => 'Tidak ada periode aktif. Aktifkan periode terlebih dahulu sebelum import siswa.']);
        }

        $import = new UsersImport;
        Excel::import($import, $request->file('file'));

        $jumlahGagal = $import->failures()->count() + $import->errors()->count();

        if ($jumlahGagal > 0) {
            $pesanGagal = $import->failures()->map(function ($failure) {
                return "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            })->implode(' | ');

            return redirect()->route('bendahara.siswa.index')
                ->with('success', "Import selesai, tapi {$jumlahGagal} baris gagal diproses.")
                ->with('alert-type', 'warning')
                ->with('alert-message', $pesanGagal);
        }

        return redirect()->route('bendahara.siswa.index')
            ->with('success', 'Data siswa berhasil di-Import')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data siswa berhasil di-Import');
    }
}