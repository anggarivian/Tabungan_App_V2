<?php

namespace App\Http\Controllers;

use PDF;
use Excel;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;
use App\Exports\TabunganExport;
use App\Exports\PengajuanExport;
use App\Exports\TransaksiExport;

class ExportController extends Controller
{
    // Export Kepsek --------------------------------------------------------------------------------------------------
    /**
    * Menangani Export Data Tabungan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    // Private function hitung saldo bersih periode aktif
    private function hitungSaldoBersih(string $pembayaran, int $periodeId): int
    {
        $baseQuery = function ($tipe) use ($pembayaran, $periodeId) {
            return \App\Models\Transaksi::where('pembayaran', $pembayaran)
                ->where('tipe_transaksi', $tipe)
                ->where('status', 'success')
                ->where('periode_id', $periodeId)
                ->whereHas('user.rombel', function ($q) use ($periodeId) {
                    $q->where('rombels.periode_id', $periodeId);
                })
                ->whereHas('user.tabungan', function ($q) use ($periodeId) {
                    $q->where('periode_id', $periodeId)->where('saldo', '>', 0);
                })
                ->sum('jumlah_transaksi');
        };

        return $baseQuery('Stor') - $baseQuery('Tarik');
    }

    // EXPORT TABUNGAN
    public function kepsek_exportTabungan(Request $request)
    {
        $validated = $request->validate([
            'export_option' => 'required|in:siswa,semua',
            'export_type'   => 'required|in:pdf,excel',
            'siswa_id'      => 'nullable|exists:users,id',
            'periode_id'    => 'required|exists:periodes,id',
        ]);

        $exportType   = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId      = $validated['siswa_id'] ?? null;
        $periodeId    = $validated['periode_id'];

        // Kueri Tabungan dengan pengecualian: Harus masuk rombel & saldo > 0
        $query = \App\Models\Tabungan::where('periode_id', $periodeId)
            ->where('saldo', '>', 0)
            ->whereHas('user.rombel', function ($q) use ($periodeId) {
                $q->where('rombels.periode_id', $periodeId);
            });

        if ($exportOption == 'siswa' && $siswaId) {
            $query->where('user_id', $siswaId);
        }

        $siswas = $query->get();
        $user   = $siswaId ? \App\Models\User::with(['rombel', 'tabungan' => function($q) use ($periodeId) { $q->where('periode_id', $periodeId); }])->find($siswaId) : null;

        // Hitung total ringkasan data valid
        $jumlahPenabungAll = \App\Models\User::where('roles_id', 4)
            ->whereHas('rombel', function ($q) use ($periodeId) {
                $q->where('rombels.periode_id', $periodeId);
            })
            ->whereHas('tabungan', function ($q) use ($periodeId) {
                $q->where('periode_id', $periodeId)->where('saldo', '>', 0);
            })->count();

        $jumlahSaldoTunaiAll   = $this->hitungSaldoBersih('Tunai', $periodeId);
        $jumlahSaldoDigitalAll = $this->hitungSaldoBersih('Digital', $periodeId);

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.tabungan', compact(
                'siswas', 'exportOption', 'user', 'periodeId',
                'jumlahPenabungAll', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'
            ));
            return $pdf->download('laporan_tabungan_periode_' . $periodeId . '.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TabunganExport($siswas, $exportType), 'laporan_tabungan.xlsx');
        }
    }

    // EXPORT TRANSAKSI
    public function kepsek_exportTransaksi(Request $request)
    {
        $validated = $request->validate([
            'export_option' => 'required|in:siswa,semua',
            'export_type'   => 'required|in:pdf,excel',
            'siswa_id'      => 'nullable|exists:users,id',
            'periode_id'    => 'required|exists:periodes,id',
        ]);

        $exportType   = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId      = $validated['siswa_id'] ?? null;
        $periodeId    = $validated['periode_id'];

        // Kueri Transaksi dengan pengecualian siswa bersaldo 0 & tanpa rombel
        $query = \App\Models\Transaksi::where('periode_id', $periodeId)->where('status', 'success')
            ->whereHas('user.rombel', function ($q) use ($periodeId) {
                $q->where('rombels.periode_id', $periodeId);
            })
            ->whereHas('user.tabungan', function ($q) use ($periodeId) {
                $q->where('periode_id', $periodeId)->where('saldo', '>', 0);
            });

        if ($exportOption == 'siswa' && $siswaId) {
            $query->where('user_id', $siswaId);
        }

        $transaksis = $query->get();
        $user = $siswaId ? \App\Models\User::find($siswaId) : null;

        $jumlahPenabungAll     = \App\Models\User::where('roles_id', 4)->whereHas('rombel', function ($q) use ($periodeId) { $q->where('rombels.periode_id', $periodeId); })->whereHas('tabungan', function ($q) use ($periodeId) { $q->where('periode_id', $periodeId)->where('saldo', '>', 0); })->count();
        $jumlahSaldoTunaiAll   = $this->hitungSaldoBersih('Tunai', $periodeId);
        $jumlahSaldoDigitalAll = $this->hitungSaldoBersih('Digital', $periodeId);

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.transaksi', compact(
                'transaksis', 'user', 'exportOption', 'periodeId',
                'jumlahPenabungAll', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'
            ));
            return $pdf->download('laporan_transaksi_periode_' . $periodeId . '.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TransaksiExport($transaksis, $exportType), 'laporan_transaksi.xlsx');
        }
    }

    // EXPORT PENGAJUAN
    public function kepsek_exportPengajuan(Request $request)
    {
        $validated = $request->validate([
            'export_option' => 'required|in:siswa,semua',
            'export_type'   => 'required|in:pdf,excel',
            'siswa_id'      => 'nullable|exists:users,id',
            'periode_id'    => 'required|exists:periodes,id',
        ]);

        $exportType   = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId      = $validated['siswa_id'] ?? null;
        $periodeId    = $validated['periode_id'];

        // Kueri Pengajuan dengan pengecualian siswa bersaldo 0 & tanpa rombel
        $query = \App\Models\Pengajuan::where('periode_id', $periodeId)
            ->whereHas('user.rombel', function ($q) use ($periodeId) {
                $q->where('rombels.periode_id', $periodeId);
            })
            ->whereHas('user.tabungan', function ($q) use ($periodeId) {
                $q->where('periode_id', $periodeId)->where('saldo', '>', 0);
            });

        if ($exportOption == 'siswa' && $siswaId) {
            $query->where('user_id', $siswaId);
        }

        $pengajuans = $query->get();
        $user = $siswaId ? \App\Models\User::find($siswaId) : null;

        $jumlahPenabungAll     = \App\Models\User::where('roles_id', 4)->whereHas('rombel', function ($q) use ($periodeId) { $q->where('rombels.periode_id', $periodeId); })->whereHas('tabungan', function ($q) use ($periodeId) { $q->where('periode_id', $periodeId)->where('saldo', '>', 0); })->count();
        $jumlahSaldoTunaiAll   = $this->hitungSaldoBersih('Tunai', $periodeId);
        $jumlahSaldoDigitalAll = $this->hitungSaldoBersih('Digital', $periodeId);

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.pengajuan', compact(
                'pengajuans', 'user', 'exportOption', 'periodeId',
                'jumlahPenabungAll', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'
            ));
            return $pdf->download('laporan_pengajuan_periode_' . $periodeId . '.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new PengajuanExport($pengajuans, $exportType), 'laporan_pengajuan.xlsx');
        }
    }

    // Export Bendahara -----------------------------------------------------------------------------------------------
    /**
    * Menangani Export Data Tabungan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_exportTabungan(Request $request)
    {
        $validated = $request->validate([
            'export_option' => 'required',
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);
        // dd($validated);
        $exportType = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId = $validated['siswa_id'];
        $kelasId = $validated['kelas_id'];

        $query = Tabungan::query();

        if ($siswaId) {
            $query->where('user_id', $siswaId);
        }
        if ($kelasId) {
            $query->whereHas('user.kelas', function($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            });
        }

        $siswas = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'Tunai')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'Tunai')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi') ;

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'Digital')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'Digital')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi') ;

        $jumlahSaldoTunaiAll = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.tabungan', compact('siswas', 'exportOption', 'kelasId', 'user', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_tabungan.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TabunganExport($siswas, $exportType), 'laporan_tabungan.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    /**
    * Menangani Export Data Transaksi oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_exportTransaksi(Request $request)
    {
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

        $query->where('status', 'success');
        $transaksis = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.transaksi', compact('transaksis', 'user', 'exportOption', 'kelasId', 'startDate', 'endDate', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_transaksi.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TransaksiExport($transaksis, $exportType, $user), 'laporan_transaksi.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    /**
    * Menangani Export Data Pengajuan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_exportPengajuan(Request $request)
    {
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

        $query = Pengajuan::query();

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

        $pengajuans = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'tarik')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.pengajuan', compact('pengajuans', 'user', 'exportOption', 'kelasId', 'startDate', 'endDate', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_pengajuan.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new PengajuanExport($pengajuans, $exportType, $user), 'laporan_pengajuan.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    // Export Walikelas -----------------------------------------------------------------------------------------------
    /**
    * Menangani Export Data Tabungan oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_exportTabungan(Request $request)
    {
        $validated = $request->validate([
            'export_option' => 'required',
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId = $validated['siswa_id'];
        $kelasId = auth()->user()->kelas->id;

        $query = Tabungan::query();

        if ($siswaId) {
            $query->where('user_id', $siswaId);
        } else {
            $query->whereHas('user.kelas', function($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            });
        }

        $siswas = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('pembayaran', 'tunai')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('pembayaran', 'digital')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.tabungan', compact('siswas', 'exportOption', 'kelasId', 'user', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_tabungan.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TabunganExport($siswas, $exportType), 'laporan_tabungan.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    /**
    * Menangani Export Data Transaksi oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_exportTransaksi(Request $request)
    {
        $validated = $request->validate([
            'export_option' => 'required',
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
            'date_range' => 'nullable|string',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId = $validated['siswa_id'];
        $dateRange = $validated['date_range'];
        $kelasId = auth()->user()->kelas->id;

        $startDate = null;
        $endDate = null;

        if ($dateRange) {
            list($startDate, $endDate) = explode(' - ', $dateRange);
        }

        $query = Transaksi::query();

        if ($siswaId) {
            $query->where('user_id', $siswaId);
        } else {
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

        $query->where('status', 'success');
        $transaksis = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi')
            - Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'tarik')
            ->where('status', 'success')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('pembayaran', 'tunai')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('pembayaran', 'digital')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.transaksi', compact('transaksis', 'user', 'exportOption', 'kelasId', 'startDate', 'endDate', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_transaksi.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TransaksiExport($transaksis, $exportType, $user), 'laporan_transaksi.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    // Export Siswa -----------------------------------------------------------------------------------------------
    /**
    * Menangani Export Data Tabungan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_exportTabungan(Request $request)
    {
        $validated = $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
        ]);
        $exportType = $validated['export_type'];
        $exportOption = 'siswa';
        $siswaId = $validated['siswa_id'];
        $kelasId = auth()->user()->kelas->id;

        $query = Tabungan::where('user_id', $siswaId);
        $siswas = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('pembayaran', 'tunai')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('pembayaran', 'digital')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.tabungan', compact('siswas', 'exportOption', 'kelasId', 'user', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_tabungan.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TabunganExport($siswas, $exportType), 'laporan_tabungan.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    /**
    * Menangani Export Data Transaksi oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_exportTransaksi(Request $request)
    {
        $validated = $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
            'date_range' => 'nullable|string',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = 'siswa';
        $siswaId = $validated['siswa_id'];
        $dateRange = $validated['date_range'];
        $kelasId = auth()->user()->kelas->id;

        $startDate = null;
        $endDate = null;

        if ($dateRange) {
            list($startDate, $endDate) = explode(' - ', $dateRange);
        }

        $query = Transaksi::where('user_id', $siswaId);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transaksis = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('pembayaran', 'tunai')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('pembayaran', 'digital')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.transaksi', compact('transaksis', 'user', 'exportOption', 'kelasId', 'startDate', 'endDate', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_transaksi.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new TransaksiExport($transaksis, $exportType, $user), 'laporan_transaksi.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }

    /**
    * Menangani Export Data Pengajuan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_exportPengajuan(Request $request)
    {
        $validated = $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'siswa_id' => 'nullable|exists:users,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'date_range' => 'nullable|string',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = 'siswa';
        $siswaId = $validated['siswa_id'];
        $kelasId = auth()->user()->kelas->id;
        $dateRange = $validated['date_range'];

        $startDate = null;
        $endDate = null;

        if ($dateRange) {
            list($startDate, $endDate) = explode(' - ', $dateRange);
        }

        $query = Pengajuan::where('user_id', $siswaId);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $pengajuans = $query->get();
        $user = User::find($siswaId);

        $jumlahPenabung = User::where('roles_id', 4 )->where('kelas_id', $kelasId)->count();
        $jumlahPenabungAll = User::where('roles_id', 4 )->count();
        $jumlahSaldoTunai = Transaksi::where('pembayaran', 'tunai')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->sum('jumlah_transaksi');

        $jumlahSaldoDigital = Transaksi::where('pembayaran', 'digital')
            ->where('tipe_transaksi', 'stor')
            ->whereHas('user', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->sum('jumlah_transaksi');

        $jumlahSaldoTunaiAll = Transaksi::where('pembayaran', 'tunai')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $jumlahSaldoDigitalAll = Transaksi::where('pembayaran', 'digital')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');

        $saldoTunai = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Tunai')->sum('jumlah_transaksi');
        $saldoDigital = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Digital')->sum('jumlah_transaksi');

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('export.pengajuan', compact('pengajuans', 'user', 'exportOption', 'kelasId', 'startDate', 'endDate', 'jumlahPenabung','jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll', 'saldoTunai', 'saldoDigital'));
            return $pdf->download('laporan_pengajuan.pdf');
        } elseif ($exportType == 'excel') {
            return Excel::download(new PengajuanExport($pengajuans, $exportType, $user), 'laporan_pengajuan.xlsx');
        }

        return back()->with('error', 'Invalid export type');
    }
}
