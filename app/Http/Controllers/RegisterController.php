<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman registrasi pengguna.
     *
     * @return \Illuminate\View\View Halaman registrasi dengan data yang diperlukan.
     */
    public function index(){

        $user = User::all();

        return view('auth.register', [
            'title' => 'Register',
            'active' => 'register'
        ]);
    }
}
