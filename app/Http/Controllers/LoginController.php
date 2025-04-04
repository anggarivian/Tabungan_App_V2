<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * LoginController menghandle proses autentikasi pengguna.
 *
 * @package App\Http\Controllers
 */
class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.login', [
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    /**
     * Menangani proses login pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($validatedData)) {
            $request->session()->regenerate();

            if ($validatedData['password'] === '12345') {
                return redirect('/change-password');
            }

            $role = Auth::user()->roles_id;
            $dashboardRoutes = [
                1 => '/kepsek/dashboard',
                2 => '/bendahara/dashboard',
                3 => '/walikelas/dashboard',
                4 => '/siswa/dashboard',
            ];

            return redirect()->intended($dashboardRoutes[$role] ?? '/');
        }

        return back()->with('LoginError', 'Login Gagal !');
    }


    /**
     * Menangani proses logout pengguna.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login')->with('logoutSuccess', 'Anda sudah logout!');
    }

    public function change_password()
    {
        return view('auth.change_password');
    }
    public function change_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()
            ->with('success', 'Password berhasil diubah (Tunggu 3 detik, Halaman ini akan beralih)')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Password berhasil diubah (Tunggu 3 detik, Halaman ini akan beralih)')
            ->with('alert-duration', 3000);
        }

        public function edit()
        {
            return view('auth.profile');
        }

        public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'kontak' => 'required|string|max:20',
        'alamat' => 'nullable|string|max:255',
        'orang_tua' => 'nullable|string|max:255',
        'current_password' => 'nullable|required_with:password|string',
        'password' => 'nullable|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ]);

    // Cek jika user ingin mengubah password
    if ($request->filled('password')) {
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai.');
        }

        $user->password = Hash::make($request->password);
    }

    // Update data lain
    $user->name = $request->name;
    $user->email = $request->email;
    $user->kontak = $request->kontak;
    $user->alamat = $request->alamat;
    $user->orang_tua = $request->orang_tua;

    $user->save();

    return back()->with('success', 'Profil berhasil diperbarui.');
}
}
