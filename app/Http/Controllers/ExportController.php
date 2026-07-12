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

    /**
    * Menangani Export Data Transaksi oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_exportTransaksi(Request $request)
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

    /**
    * Menangani Export Data Pengajuan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_exportPengajuan(Request $request)
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
            'rombel_id' => 'nullable|exists:rombels,id',
            'periode_id' => 'required|exists:periodes,id',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId = $validated['siswa_id'];
        $rombelIdReq = $validated['rombel_id'];
        $periodeId = $validated['periode_id'];
        $walikelasId = auth()->id();

        // Pastikan hanya bisa export rombel miliknya
        $rombelMilikSaya = \App\Models\Rombel::where('walikelas_id', $walikelasId)
                            ->where('periode_id', $periodeId);
        
        if ($rombelIdReq) {
            $rombelMilikSaya->where('id', $rombelIdReq);
        }
        
        $rombelIdsValid = $rombelMilikSaya->pluck('id');

        // Query Ambil Data Tabungan
        $query = \App\Models\Tabungan::where('periode_id', $periodeId)
                    ->whereHas('user', function ($q) use ($rombelIdsValid, $periodeId) {
                        $q->whereHas('rombel', function ($qq) use ($rombelIdsValid, $periodeId) {
                            $qq->whereIn('rombels.id', $rombelIdsValid)
                               ->where('rombels.periode_id', $periodeId);
                        });
                    });

        if ($exportOption == 'siswa' && $siswaId) {
            $query->where('user_id', $siswaId);
        }

        $siswas = $query->with('user')->get();
        $user = $siswaId ? \App\Models\User::find($siswaId) : null;
        
        // Statistik Rekapitulasi Rombel
        $jumlahPenabung = $siswas->count();
        $jumlahPenabungAll = \App\Models\User::where('roles_id', 4)->count();
        
        // Menghitung Saldo Tunai dan Digital Rombel Ini
        $totalTunaiStor = \App\Models\Transaksi::whereIn('user_id', $siswas->pluck('user_id'))->where('periode_id', $periodeId)->where('status', 'success')->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $totalTunaiTarik = \App\Models\Transaksi::whereIn('user_id', $siswas->pluck('user_id'))->where('periode_id', $periodeId)->where('status', 'success')->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
        $jumlahSaldoTunai = $totalTunaiStor - $totalTunaiTarik;

        $totalDigitalStor = \App\Models\Transaksi::whereIn('user_id', $siswas->pluck('user_id'))->where('periode_id', $periodeId)->where('status', 'success')->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');
        $totalDigitalTarik = \App\Models\Transaksi::whereIn('user_id', $siswas->pluck('user_id'))->where('periode_id', $periodeId)->where('status', 'success')->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');
        $jumlahSaldoDigital = $totalDigitalStor - $totalDigitalTarik;
        
        // Dummy variable jika export blade mu butuh ini (bisa diabaikan di pdf-nya nanti)
        $jumlahSaldoTunaiAll = 0; $jumlahSaldoDigitalAll = 0;

        if ($exportType == 'pdf') {
            $pdf = \PDF::loadView('export.tabungan', compact('siswas', 'exportOption', 'user', 'jumlahPenabung', 'jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_tabungan_kelas.pdf');
        } elseif ($exportType == 'excel') {
            return \Excel::download(new \App\Exports\TabunganExport($siswas, $exportType), 'laporan_tabungan_kelas.xlsx');
        }

        return back()->with('error', 'Tipe export tidak valid');
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
            'rombel_id' => 'nullable|exists:rombels,id',
            'periode_id' => 'required|exists:periodes,id',
            'date_range' => 'nullable|string',
        ]);

        $exportType = $validated['export_type'];
        $exportOption = $validated['export_option'];
        $siswaId = $validated['siswa_id'];
        $rombelIdReq = $validated['rombel_id'];
        $periodeId = $validated['periode_id'];
        $walikelasId = auth()->id();

        // Pastikan hanya bisa export rombel miliknya
        $rombelMilikSaya = \App\Models\Rombel::where('walikelas_id', $walikelasId)
                            ->where('periode_id', $periodeId);
        
        if ($rombelIdReq) {
            $rombelMilikSaya->where('id', $rombelIdReq);
        }
        $rombelIdsValid = $rombelMilikSaya->pluck('id');

        // Query Ambil Data Transaksi
        $query = \App\Models\Transaksi::where('periode_id', $periodeId)
                    ->where('status', 'success')
                    ->whereHas('user', function ($q) use ($rombelIdsValid, $periodeId) {
                        $q->whereHas('rombel', function ($qq) use ($rombelIdsValid, $periodeId) {
                            $qq->whereIn('rombels.id', $rombelIdsValid)
                               ->where('rombels.periode_id', $periodeId);
                        });
                    });

        if ($exportOption == 'siswa' && $siswaId) {
            $query->where('user_id', $siswaId);
        }

        $transaksis = $query->with('user')->orderBy('created_at', 'desc')->get();
        $user = $siswaId ? \App\Models\User::find($siswaId) : null;

        // Ambil Unique Users dari hasil transaksi untuk hitung penabung aktif
        $uniqueUsers = $transaksis->pluck('user_id')->unique();
        $jumlahPenabung = $uniqueUsers->count();
        $jumlahPenabungAll = \App\Models\User::where('roles_id', 4)->count();
        
        // Statistik berdasar hasil query transaksi yang terekspor
        $jumlahSaldoTunai = $transaksis->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi')
                          - $transaksis->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');
                          
        $jumlahSaldoDigital = $transaksis->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Digital')->sum('jumlah_transaksi')
                            - $transaksis->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        // Dummy variable jika export blade mu butuh ini
        $jumlahSaldoTunaiAll = 0; $jumlahSaldoDigitalAll = 0;

        if ($exportType == 'pdf') {
            $pdf = \PDF::loadView('export.transaksi', compact('transaksis', 'user', 'exportOption', 'jumlahPenabung', 'jumlahPenabungAll', 'jumlahSaldoTunai', 'jumlahSaldoDigital', 'jumlahSaldoTunaiAll', 'jumlahSaldoDigitalAll'));
            return $pdf->download('laporan_transaksi_kelas.pdf');
        } elseif ($exportType == 'excel') {
            return \Excel::download(new \App\Exports\TransaksiExport($transaksis, $exportType, $user), 'laporan_transaksi_kelas.xlsx');
        }

        return back()->with('error', 'Tipe export tidak valid');
    }

    // Export Siswa -----------------------------------------------------------------------------------------------
    /**
    * Menangani Export Data Tabungan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    private function hitungSaldoBersihSiswa(int $userId, int $periodeId, string $pembayaran): int
    {
        $base = function ($tipe) use ($userId, $periodeId, $pembayaran) {
            return \App\Models\Transaksi::where('user_id', $userId)
                ->where('periode_id', $periodeId)
                ->where('pembayaran', $pembayaran)
                ->where('tipe_transaksi', $tipe) // 'Stor' atau 'Tarik'
                ->where('status', 'success')
                ->sum('jumlah_transaksi');
        };

        // Menggunakan kapital huruf depan ('Stor' dan 'Tarik') agar sesuai dengan standar database sebelumnya
        return $base('Stor') - $base('Tarik'); 
    }

    public function siswa_exportTabungan(Request $request)
    {
        $validated = $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'periode_id'  => 'required|exists:periodes,id',
        ]);

        $exportType = $validated['export_type'];
        $periodeId  = $validated['periode_id'];
        $user       = auth()->user();

        // Tetapkan exportOption secara statis karena ini khusus siswa
        $exportOption = 'siswa';

        $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
            ->where('periode_id', $periodeId)
            ->first();

        $siswas = collect($tabungan ? [$tabungan] : []);

        $rombelInfo = $user->rombels()->wherePivot('periode_id', $periodeId)->first();

        $jumlahSaldoTunai   = $this->hitungSaldoBersihSiswa($user->id, $periodeId, 'Tunai');
        $jumlahSaldoDigital = $this->hitungSaldoBersihSiswa($user->id, $periodeId, 'Digital');

        if ($exportType == 'pdf') {
            $pdf = \PDF::loadView('export.tabungan', compact(
                'siswas', 'user', 'periodeId', 'rombelInfo',
                'jumlahSaldoTunai', 'jumlahSaldoDigital', 'exportOption' // <-- Tambahkan di sini
            ));
            return $pdf->download('buku_tabungan_saya.pdf');
        } elseif ($exportType == 'excel') {
            return \Excel::download(new \App\Exports\TabunganExport($siswas, $exportType), 'buku_tabungan_saya.xlsx');
        }

        return back()->with('error', 'Tipe export tidak valid');
    }

    public function siswa_exportTransaksi(Request $request)
    {
        $validated = $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'periode_id'  => 'required|exists:periodes,id',
        ]);

        $exportType = $validated['export_type'];
        $periodeId  = $validated['periode_id'];
        $user       = auth()->user();
        
        // Definisikan exportOption
        $exportOption = 'siswa';

        $transaksis = \App\Models\Transaksi::where('user_id', $user->id)
            ->where('periode_id', $periodeId)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->get();

        $jumlahSaldoTunai   = $this->hitungSaldoBersihSiswa($user->id, $periodeId, 'Tunai');
        $jumlahSaldoDigital = $this->hitungSaldoBersihSiswa($user->id, $periodeId, 'Digital');

        if ($exportType == 'pdf') {
            $pdf = \PDF::loadView('export.transaksi', compact(
                'transaksis', 'user', 'periodeId', 'exportOption', // Tambahkan exportOption
                'jumlahSaldoTunai', 'jumlahSaldoDigital'
            ));
            return $pdf->download('riwayat_transaksi_saya.pdf');
        } elseif ($exportType == 'excel') {
            return \Excel::download(new \App\Exports\TransaksiExport($transaksis, $exportType, $user), 'riwayat_transaksi_saya.xlsx');
        }

        return back()->with('error', 'Tipe export tidak valid');
    }

    public function siswa_exportPengajuan(Request $request)
    {
        $validated = $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'periode_id'  => 'required|exists:periodes,id',
        ]);

        $exportType = $validated['export_type'];
        $periodeId  = $validated['periode_id'];
        $user       = auth()->user();
        
        // Definisikan exportOption
        $exportOption = 'siswa';

        $pengajuans = \App\Models\Pengajuan::where('user_id', $user->id)
            ->where('periode_id', $periodeId)
            ->orderBy('created_at', 'desc')
            ->get();

        $jumlahSaldoTunai   = $this->hitungSaldoBersihSiswa($user->id, $periodeId, 'Tunai');
        $jumlahSaldoDigital = $this->hitungSaldoBersihSiswa($user->id, $periodeId, 'Digital');

        if ($exportType == 'pdf') {
            $pdf = \PDF::loadView('export.pengajuan', compact(
                'pengajuans', 'user', 'periodeId', 'exportOption', // Tambahkan exportOption
                'jumlahSaldoTunai', 'jumlahSaldoDigital'
            ));
            return $pdf->download('riwayat_pengajuan_saya.pdf');
        } elseif ($exportType == 'excel') {
            return \Excel::download(new \App\Exports\PengajuanExport($pengajuans, $exportType, $user), 'riwayat_pengajuan_saya.xlsx');
        }

        return back()->with('error', 'Tipe export tidak valid');
    }
}
