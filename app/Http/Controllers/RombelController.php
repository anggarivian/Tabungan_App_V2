<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index()
    {
        $user = User::where('roles_id', 4)->get();

        return view('bendahara.rombel.index');
    }
}
