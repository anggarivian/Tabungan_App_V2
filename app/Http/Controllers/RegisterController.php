<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(){
        $user = User::all();
        return view('auth.register', [
            'title' => 'Register',
            'active' => 'register'
        ]);
    }
}
