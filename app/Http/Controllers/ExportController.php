<?php

namespace App\Http\Controllers;

use PDF;
use Excel;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Exports\TabunganExport;
use App\Exports\PengajuanExport;
use App\Exports\TransaksiExport;

class ExportController extends Controller
{
    // Export Bendahara -----------------------------------------------------------------------------------------------
    public function bendahara_exportTransaksi(Request $request)
    {
        // Validasi request
        $validated = $request->validate([
            'export_option' => 'required',
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'date_range' => 'nullable|string',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId = $validated['siswa_id'];
        $kelasId = $validated['kelas_id'];
        $dateRange = $validated['date_range'];

        $startDate = null;
        $endDate = null;

        if ($dateRange) {
            list($startDate, $endDate) = explode(' - ', $dateRange);
        }

        $query = Transaksi::query();

        if ($siswaId) {
            $query->where('user_id', $siswaId);
        }
        if ($kelasId) {
            $query->whereHas('user.kelas', function($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            });
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transaksis = $query->get();
        $user = User::find($siswaId);

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.transaksi', compact('transaksis', 'user', 'exportOption', 'kelasId', 'startDate', 'endDate'));
            return $pdf->download('laporan_transaksi.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TransaksiExport($transaksis, $exportType, $user), 'laporan_transaksi.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

}
