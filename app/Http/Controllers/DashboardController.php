<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * DashboardController menghandle tampilan dashboard untuk berbagai role.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk role Kepsek.
     *
     * @return \Illuminate\View\View
     */
    public function kepsek(){
        return view('kepsek.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }

    /**
     * Menampilkan dashboard untuk role Bendahara.
     *
     * @return \Illuminate\View\View
     */
    public function bendahara(){
        return view('bendahara.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }

    /**
     * Menampilkan dashboard untuk role Walikelas.
     *
     * @return \Illuminate\View\View
     */
    public function walikelas(){
        return view('walikelas.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }

    /**
     * Menampilkan dashboard untuk role Siswa.
     *
     * @return \Illuminate\View\View
     */
    public function siswa(){
        return view('siswa.index', [
            'title' => 'Dashboard',
            'active' => 'dashboard'
        ]);
    }
}
