<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export()
    {
        return view('bendahara.laporan.export');
    }
}
