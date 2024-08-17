<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TabunganController extends Controller
{
    public function index()
    {
        return view('bendahara.tabungan.index');
    }
    public function stor()
    {
        return view('bendahara.tabungan.stor');
    }
}
