<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class WalikelasController extends Controller
{
    public function index(Request $request){
        $kelas = Kelas::all();

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

        $user = $query->paginate(10);

        return view('bendahara.kelola_walikelas', compact('user','kelas'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->kontak = $request->kontak;
        $user->alamat = $request->alamat;
        $user->kelas_id = $request->kelas;
        $user->roles_id = 3;
        $user->save();

        return redirect()->route('walikelas.index')->with('success', 'Data walikelas berhasil ditambahkan')->with('alert-type', 'success')->with('alert-message', 'Data walikelas berhasil ditambahkan')->with('alert-duration', 3000);
    }

    public function getWalikelasData($id)
    {
        $walikelas = User::findOrFail($id);
        return response()->json($walikelas);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$request->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$request->id,
            'jenis_kelamin' => 'required|in:L,P',
            'kontak' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->kontak = $request->kontak;
        $user->alamat = $request->alamat;
        $user->kelas_id = $request->kelas;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8'
            ]);
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('walikelas.index')->with('success', 'Data walikelas berhasil diperbarui')->with('alert-type', 'warning')->with('alert-message', 'Data walikelas berhasil diperbarui')->with('alert-duration', 3000);
    }

    public function delete($id)
    {
        $walikelas = User::findOrFail($id);
        $walikelas->delete();

        return redirect()->route('walikelas.index')
            ->with('success', 'Data walikelas berhasil dihapus')
            ->with('alert-type', 'danger')
            ->with('alert-message', 'Data walikelas berhasil dihapus')
            ->with('alert-duration', 3000);
    }
}
