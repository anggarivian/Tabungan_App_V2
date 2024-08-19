<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $role = Auth::user()->roles_id;
            $dashboardRoutes = [
                1 => '/kepsek/dashboard',
                2 => '/bendahara/dashboard',
                3 => '/walikelas/dashboard',
                4 => '/siswa/dashboard',
            ];

            return redirect()->intended($dashboardRoutes[$role] ?? '/home');
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
}
