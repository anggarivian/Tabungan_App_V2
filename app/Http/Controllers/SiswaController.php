<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Tabungan;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dan melakukan pencarian berdasarkan nama, email, alamat, username, atau kontak.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $kelas = Kelas::all();

        $query = User::query()->where('roles_id', 4);
        $query->select('id','name','email','username','jenis_kelamin','kontak','password','orang_tua','alamat','kelas_id','roles_id','created_at','updated_at');
        $searchTerm = $request->input('search');

        if (!empty($searchTerm)) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('email', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('alamat', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('username', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('kontak', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('alamat', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        $query->orderBy('created_at','desc');

        $user = $query->paginate(10);

        return view('bendahara.kelola_siswa', compact('user','kelas'));
    }

    /**
     * Menambahkan data siswa baru ke dalam basis data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|string|max:15',
            'alamat' => 'required|string',
            'orang_tua' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'kontak.required' => 'Kontak harus diisi.',
            'kontak.max' => 'Kontak maksimal 15 karakter.',
            'alamat.required' => 'Alamat harus diisi.',
            'orang_tua.required' => 'Nama orang tua harus diisi.',
        ]);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = bcrypt($validatedData['password']);
        $user->jenis_kelamin = $validatedData['jenis_kelamin'];
        $user->kontak = $validatedData['kontak'];
        $user->alamat = $validatedData['alamat'];
        $user->orang_tua = $validatedData['orang_tua'];
        $user->kelas_id = $request->kelas;
        $user->roles_id = 4;

        $kelasMap = [ 1 => '1A', 2 => '1B', 3 => '2A', 4 => '2B', 5 => '3A', 6 => '3B', 7 => '40', 8 => '5', 9 => '60',];

        $existingUser = User::where('username', 'like', $kelasMap[$request->kelas] . '%')->orderBy('username', 'desc')->first();
        if ($existingUser) {
            $nextNumber = (int)substr($existingUser->username, -3) + 1;
            $user->username = $kelasMap[$request->kelas] . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $user->username = $kelasMap[$request->kelas] . '001';
        }
        $user->save();

        $tabungan = new Tabungan ;
        $tabungan->saldo = 0 ;
        $tabungan->premi = 0 ;
        $tabungan->sisa = 0 ;
        $tabungan->user_id = User::latest()->first()->id;

        $tabungan->save();

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan')->with('alert-type', 'success')->with('alert-message', 'Data siswa berhasil ditambahkan')->with('alert-duration', 3000);
    }

    /**
     * Mengambil data siswa berdasarkan ID dan mengembalikannya dalam format JSON.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiswaData($id)
    {
        $siswa = User::findOrFail($id);
        return response()->json($siswa);
    }

    /**
     * Mengedit data siswa yang sudah ada berdasarkan ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$request->id,
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|string|max:15',
            'alamat' => 'required|string',
            'orang_tua' => 'required|string',
            'kelas' => 'required|integer',
            'password' => 'sometimes|required|string|min:8',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'kontak.required' => 'Kontak harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'orang_tua.required' => 'Orang tua harus diisi',
            'kelas.required' => 'Kelas harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->jenis_kelamin = $validatedData['jenis_kelamin'];
        $user->kontak = $validatedData['kontak'];
        $user->alamat = $validatedData['alamat'];
        $user->orang_tua = $validatedData['orang_tua'];
        $user->kelas_id = $validatedData['kelas'];

        if ($validatedData['password']) {
            $user->password = bcrypt($validatedData['password']);
        }

        $user->save();

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui')->with('alert-type', 'warning')->with('alert-message', 'Data siswa berhasil diperbarui')->with('alert-duration', 3000);
    }

    /**
     * Menghapus data siswa berdasarkan ID.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $siswa = User::findOrFail($id);
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus')
            ->with('alert-type', 'danger')
            ->with('alert-message', 'Data siswa berhasil dihapus')
            ->with('alert-duration', 3000);
    }
}
