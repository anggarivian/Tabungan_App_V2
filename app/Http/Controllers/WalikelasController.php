<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class WalikelasController extends Controller
{
    /**
     * Menampilkan daftar walikelas dan melakukan pencarian berdasarkan nama, email, alamat, username, atau kontak.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){
        $kelas = Kelas::all();
        $perPage = request('perPage', 10);

        $query = User::query()->where('roles_id', 3);
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

        $user = $query->paginate($perPage);

        return view('bendahara.kelola_walikelas', compact('user','kelas'));
    }

    /**
     * Menambahkan data walikelas baru ke dalam basis data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|numeric|min:1000000000|max:999999999999999',
            'alamat' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'kontak.required' => 'Kontak harus diisi.',
            'kontak.max' => 'Kontak maksimal 15 karakter.',
            'alamat.required' => 'Alamat harus diisi.',
        ]);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->password = bcrypt($validatedData['password']);
        $user->jenis_kelamin = $validatedData['jenis_kelamin'];
        $user->kontak = $validatedData['kontak'];
        $user->alamat = $validatedData['alamat'];
        $user->kelas_id = $request->kelas;
        $user->roles_id = 3;
        $user->save();

        return redirect()->route('bendahara.walikelas.index')->with('success', 'Data walikelas berhasil ditambahkan')->with('alert-type', 'success')->with('alert-message', 'Data walikelas berhasil ditambahkan')->with('alert-duration', 3000);
    }

    /**
     * Mengambil data walikelas berdasarkan ID dan mengembalikannya dalam format JSON.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function getWalikelasData($id)
    {
        $walikelas = User::findOrFail($id);
        return response()->json($walikelas);
    }

    /**
     * Mengedit data walikelas yang sudah ada berdasarkan ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$request->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$request->id,
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|numeric|min:1000000000|max:999999999999999',
            'alamat' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'email.required' => 'Email harus diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi.',
            'kontak.required' => 'Kontak harus diisi.',
            'alamat.required' => 'Alamat harus diisi.',
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $validatedData['name'];
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->jenis_kelamin = $validatedData['jenis_kelamin'];
        $user->kontak = $validatedData['kontak'];
        $user->alamat = $validatedData['alamat'];
        $user->kelas_id = $request->kelas;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8'
            ]);
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('bendahara.walikelas.index')->with('success', 'Data walikelas berhasil diperbarui')->with('alert-type', 'warning')->with('alert-message', 'Data walikelas berhasil diperbarui')->with('alert-duration', 3000);
    }

    /**
     * Menghapus data walikelas berdasarkan ID.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function delete($id)
    {
        $walikelas = User::findOrFail($id);
        $walikelas->delete();

        return redirect()->route('bendahara.walikelas.index')
            ->with('success', 'Data walikelas berhasil dihapus')
            ->with('alert-type', 'danger')
            ->with('alert-message', 'Data walikelas berhasil dihapus')
            ->with('alert-duration', 3000);
    }
}
