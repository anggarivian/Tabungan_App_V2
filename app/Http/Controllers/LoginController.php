<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('auth.login', [
            'title' => 'Login',
            'active' => 'login'
        ]);
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]); 
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
    
            // Dapatkan role pengguna
            $role = Auth::user()->roles_id; // Pastikan kolom `role` ada di tabel `users`
    
            // Redirect berdasarkan role
            switch ($role) {
                case 1:
                    return redirect()->intended('/kepsek/dashboard');
                case 2:
                    return redirect()->intended('/bendahara/dashboard');
                case 3:
                    return redirect()->intended('/walikelas/dashboard');
                case 4:
                    return redirect()->intended('/siswa/dashboard');
                default:
                    return redirect('/home'); // Default redirect jika role tidak cocok
            }
        }
    
        return back()->with('LoginError', 'Login Gagal !');
    }    

    public function logout() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login')->with('logoutSuccess', 'Anda sudah logout!');
    }
}
