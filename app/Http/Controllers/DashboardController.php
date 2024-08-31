<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * DashboardController menghandle tampilan dashboard untuk berbagai role.
 */
class DashboardController extends Controller
{

    public function kepsek(){
        return view('kepsek.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }

    public function bendahara(){
        return view('bendahara.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }

    public function walikelas(){
        return view('walikelas.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }

    public function siswa(){
        return view('siswa.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }
}
