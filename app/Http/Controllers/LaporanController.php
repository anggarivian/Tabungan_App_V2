<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Periode;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // Laporan Kepsek ----------------------------------------------------------------------------------
    /**
    * Menampilkan Data Laporan Tabungan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_tabungan(Request $request)
    {
        $search    = $request->input('search');
        $rombelId  = $request->input('rombel');
        $sortSaldo = $request->input('sort_saldo');
        $perPage   = $request->input('perPage', 10);
        
        // Cek filter periode, kalau kosong pakai periode aktif sekarang
        $periodeReq = $request->input('periode_id');
        $selectedPeriodeId = $periodeReq ?: \App\Helpers\PeriodeHelper::id();

        // Query utama: Ambil user dengan Role 4 (Siswa)
        $query = \App\Models\User::where('roles_id', 4)
            ->with(['rombel' => function($q) use ($selectedPeriodeId) {
                // Pastikan Rombel yang ditarik adalah rombel di periode yang dipilih
                $q->where('rombels.periode_id', $selectedPeriodeId);
            }, 'tabungan' => function ($q) use ($selectedPeriodeId) {
                // Tarik keranjang tabungan sesuai periode yang dipilih
                $q->where('periode_id', $selectedPeriodeId);
            }])
            ->whereHas('rombel', function ($q) use ($selectedPeriodeId) {
                // Pastikan siswa ini memang terdaftar di rombel pada periode tersebut
                $q->where('rombels.periode_id', $selectedPeriodeId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombelId, function ($q) use ($rombelId, $selectedPeriodeId) {
                $q->whereHas('rombel', function ($qq) use ($rombelId, $selectedPeriodeId) {
                    $qq->where('rombels.id', $rombelId)
                      ->where('rombels.periode_id', $selectedPeriodeId);
                });
            })
            // Gunakan withSum untuk dapet total_saldo biar bisa di-sort
            ->withSum(['tabungan as total_saldo' => function ($q) use ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            }], 'saldo');

        // Sorting
        if (in_array($sortSaldo, ['asc', 'desc'])) {
            $query->orderBy('total_saldo', $sortSaldo);
        } else {
            // Default sort by Nama
            $query->orderBy('name', 'asc');
        }

        // Kalkulasi Card Summary
        $totalSiswa = (clone $query)->count();
        $totalSaldoKeseluruhan = (clone $query)->get()->sum('total_saldo');

        $user = $query->paginate($perPage)->withQueryString();

        // Data untuk Dropdown Filter
        $rombelList = \App\Models\Rombel::where('periode_id', $selectedPeriodeId)->orderBy('name')->get();
        $periodeList = \App\Models\Periode::orderBy('id', 'desc')->get();

        return view('kepsek.laporan.lap_tabungan', compact(
            'user', 'rombelList', 'periodeList', 'totalSiswa', 'totalSaldoKeseluruhan', 'selectedPeriodeId'
        ));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_transaksi(Request $request)
    {
        $request->validate([
            'search'          => 'nullable|string|max:100',
            'rombel_id'       => 'nullable|integer|exists:rombels,id', // Diubah ke rombel_id
            'tipe_transaksi'  => 'nullable|in:stor,tarik',
            'tipe_pembayaran' => 'nullable|in:online,offline',
            'sort_tanggal'    => 'nullable|in:asc,desc',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'perPage'         => 'nullable|integer|in:10,25,50,75,100',
            'periode_id'      => 'nullable|integer|exists:periodes,id',
        ]);

        $search         = $request->input('search');
        $rombelId       = $request->input('rombel_id'); // Diubah dari kelas ke rombel
        $tipeTransaksi  = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $sortTanggal    = $request->input('sort_tanggal', 'desc');
        $startDate      = $request->input('start_date');
        $endDate        = $request->input('end_date');
        $perPage        = $request->input('perPage', 10);
        
        // Dropdown list periode
        $periodeList = Periode::orderByDesc('id')->get();
        // Gunakan periode terpilih, fallback ke periode aktif saat ini jika kosong
        $selectedPeriodeId = $request->input('periode_id', PeriodeHelper::id());

        $query = Transaksi::query()
            ->with([
                'user',
                // Load pivot data (kode) dari rombel_user berdasarkan periode terpilih
                'user.rombels' => function ($q) use ($selectedPeriodeId) {
                    $q->where('rombels.periode_id', $selectedPeriodeId)
                    ->withPivot('kode'); // Ambil kolom 'kode' dari table pivot
                },
                'user.rombels.kelas',
            ])
            ->where('periode_id', $selectedPeriodeId) // Filter mengikuti periode terpilih
            ->where('status', 'success')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombelId, function ($q) use ($rombelId, $selectedPeriodeId) {
                $q->whereHas('user.rombels', function ($qq) use ($rombelId, $selectedPeriodeId) {
                    $qq->where('rombel_id', $rombelId)
                    ->where('rombels.periode_id', $selectedPeriodeId);
                });
            })
            ->when($tipeTransaksi, fn ($q) => $q->where('tipe_transaksi', $tipeTransaksi))
            ->when($tipePembayaran, fn ($q) => $q->where('pembayaran', $tipePembayaran))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at', $sortTanggal === 'asc' ? 'asc' : 'desc');

        $totalTransaksi = (clone $query)->count();
        $totalStor      = (clone $query)->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $totalTarik     = (clone $query)->where('tipe_transaksi', 'tarik')->sum('jumlah_transaksi');

        $transaksi = $query->paginate($perPage)->withQueryString();
        
        // Tarik daftar rombel dinamis hanya untuk periode yang sedang dipilih
        $rombelList = Rombel::where('periode_id', $selectedPeriodeId)->orderBy('name')->get();

        return view('kepsek.laporan.lap_transaksi', compact(
            'transaksi', 'rombelList', 'totalTransaksi', 'totalStor', 'totalTarik', 'periodeList', 'selectedPeriodeId'
        ));
    }

    /**
    * Menampilkan Data Laporan Pengajuan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_pengajuan(Request $request)
    {
        $search        = $request->input('search');
        $rombel        = $request->input('rombel');
        $status        = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');
        $sortDate      = $request->input('sort_date', 'desc');
        $startDate     = $request->input('start_date');
        $endDate       = $request->input('end_date');
        $perPage       = $request->input('perPage', 10);

        $periodeList   = \App\Models\Periode::orderBy('tanggal_mulai', 'desc')->get();
        $periodeId     = $request->input('periode_id', \App\Helpers\PeriodeHelper::id());

        // Rombel yang tersedia mengikuti periode yang lagi dipilih
        $rombelList    = \App\Models\Rombel::where('periode_id', $periodeId)->orderBy('name')->get();

        $query = \App\Models\Pengajuan::with(['user.rombels.kelas', 'user.tabungan' => function ($q) use ($periodeId) {
                $q->where('periode_id', $periodeId);
            }])
            ->where('periode_id', $periodeId)
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombel, function ($q) use ($rombel, $periodeId) {
                $q->whereHas('user.rombels', function ($qq) use ($rombel, $periodeId) {
                    $qq->where('rombels.id', $rombel)
                       ->where('rombels.periode_id', $periodeId);
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($sortPenarikan, function ($q) use ($sortPenarikan) {
                $q->orderBy('jumlah_penarikan', $sortPenarikan);
            });

        if (!$sortPenarikan) {
            if (in_array($sortDate, ['asc', 'desc'])) {
                $query->orderBy('created_at', $sortDate);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        }

        $totalPengajuan = (clone $query)->count();
        $totalPending   = (clone $query)->where('status', 'Pending')->count();
        $totalDiterima  = (clone $query)->whereIn('status', ['Terima', 'Diterima', 'SUCCEEDED'])->count();
        $totalDitolak   = (clone $query)->whereIn('status', ['Tolak', 'tolak'])->count();

        $pengajuan = $query->paginate($perPage)->withQueryString();

        return view('kepsek.laporan.lap_pengajuan', compact(
            'pengajuan', 'rombelList', 'periodeList', 'periodeId',
            'totalPengajuan', 'totalPending', 'totalDiterima', 'totalDitolak'
        ));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi, Tabungan, dan Pengajuan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_export(Request $request)
    {
        $periodeId = PeriodeHelper::id();

        $siswas = User::where('roles_id', 4)
            ->whereHas('rombels', function ($q) use ($periodeId) {
                // Tambahkan nama tabel spesifik di sini (contoh: rombels.periode_id)
                $q->where('rombels.periode_id', $periodeId); 
            })
            ->orderBy('name')
            ->get();

        $rombels = Rombel::where('periode_id', $periodeId)
            ->orderBy('name')
            ->get();

        // Jangan lupa pastikan $periodes juga dikirim jika kamu memakai dropdown filter periode dari jawaban sebelumnya
        $periodes = \App\Models\Periode::orderBy('tanggal_mulai', 'desc')->get();

        return view('kepsek.laporan.export', compact('siswas', 'rombels', 'periodes'));
    }

    // Laporan Bendahara --------------------------------------------------------------------------------
    /**
    * Menampilkan Data Laporan Tabungan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_tabungan(Request $request){
        $search    = $request->input('search');
        $rombelId  = $request->input('rombel');
        $sortSaldo = $request->input('sort_saldo');
        $perPage   = $request->input('perPage', 10);
        
        // Cek filter periode, kalau kosong pakai periode aktif sekarang
        $periodeReq = $request->input('periode_id');
        $selectedPeriodeId = $periodeReq ?: \App\Helpers\PeriodeHelper::id();

        // Query utama: Ambil user dengan Role 4 (Siswa)
        $query = \App\Models\User::where('roles_id', 4)
            ->with(['rombel' => function($q) use ($selectedPeriodeId) {
                // Pastikan Rombel yang ditarik adalah rombel di periode yang dipilih
                $q->where('rombels.periode_id', $selectedPeriodeId);
            }, 'tabungan' => function ($q) use ($selectedPeriodeId) {
                // Tarik keranjang tabungan sesuai periode yang dipilih
                $q->where('periode_id', $selectedPeriodeId);
            }])
            ->whereHas('rombel', function ($q) use ($selectedPeriodeId) {
                // Pastikan siswa ini memang terdaftar di rombel pada periode tersebut
                $q->where('rombels.periode_id', $selectedPeriodeId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombelId, function ($q) use ($rombelId, $selectedPeriodeId) {
                $q->whereHas('rombel', function ($qq) use ($rombelId, $selectedPeriodeId) {
                    $qq->where('rombels.id', $rombelId)
                      ->where('rombels.periode_id', $selectedPeriodeId);
                });
            })
            // Gunakan withSum untuk dapet total_saldo biar bisa di-sort
            ->withSum(['tabungan as total_saldo' => function ($q) use ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            }], 'saldo');

        // Sorting
        if (in_array($sortSaldo, ['asc', 'desc'])) {
            $query->orderBy('total_saldo', $sortSaldo);
        } else {
            // Default sort by Nama
            $query->orderBy('name', 'asc');
        }

        // Kalkulasi Card Summary
        $totalSiswa = (clone $query)->count();
        $totalSaldoKeseluruhan = (clone $query)->get()->sum('total_saldo');

        $user = $query->paginate($perPage)->withQueryString();

        // Data untuk Dropdown Filter
        $rombelList = \App\Models\Rombel::where('periode_id', $selectedPeriodeId)->orderBy('name')->get();
        $periodeList = \App\Models\Periode::orderBy('id', 'desc')->get();

        return view('bendahara.laporan.lap_tabungan', compact(
            'user', 'rombelList', 'periodeList', 'totalSiswa', 'totalSaldoKeseluruhan', 'selectedPeriodeId'
        ));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_transaksi(Request $request) {
        $request->validate([
            'search'          => 'nullable|string|max:100',
            'rombel_id'       => 'nullable|integer|exists:rombels,id', // Diubah ke rombel_id
            'tipe_transaksi'  => 'nullable|in:stor,tarik',
            'tipe_pembayaran' => 'nullable|in:online,offline',
            'sort_tanggal'    => 'nullable|in:asc,desc',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'perPage'         => 'nullable|integer|in:10,25,50,75,100',
            'periode_id'      => 'nullable|integer|exists:periodes,id',
        ]);

        $search         = $request->input('search');
        $rombelId       = $request->input('rombel_id'); // Diubah dari kelas ke rombel
        $tipeTransaksi  = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $sortTanggal    = $request->input('sort_tanggal', 'desc');
        $startDate      = $request->input('start_date');
        $endDate        = $request->input('end_date');
        $perPage        = $request->input('perPage', 10);
        
        // Dropdown list periode
        $periodeList = Periode::orderByDesc('id')->get();
        // Gunakan periode terpilih, fallback ke periode aktif saat ini jika kosong
        $selectedPeriodeId = $request->input('periode_id', PeriodeHelper::id());

        $query = Transaksi::query()
            ->with([
                'user',
                // Load pivot data (kode) dari rombel_user berdasarkan periode terpilih
                'user.rombels' => function ($q) use ($selectedPeriodeId) {
                    $q->where('rombels.periode_id', $selectedPeriodeId)
                    ->withPivot('kode'); // Ambil kolom 'kode' dari table pivot
                },
                'user.rombels.kelas',
            ])
            ->where('periode_id', $selectedPeriodeId) // Filter mengikuti periode terpilih
            ->where('status', 'success')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombelId, function ($q) use ($rombelId, $selectedPeriodeId) {
                $q->whereHas('user.rombels', function ($qq) use ($rombelId, $selectedPeriodeId) {
                    $qq->where('rombel_id', $rombelId)
                    ->where('rombels.periode_id', $selectedPeriodeId);
                });
            })
            ->when($tipeTransaksi, fn ($q) => $q->where('tipe_transaksi', $tipeTransaksi))
            ->when($tipePembayaran, fn ($q) => $q->where('pembayaran', $tipePembayaran))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at', $sortTanggal === 'asc' ? 'asc' : 'desc');

        $totalTransaksi = (clone $query)->count();
        $totalStor      = (clone $query)->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $totalTarik     = (clone $query)->where('tipe_transaksi', 'tarik')->sum('jumlah_transaksi');

        $transaksi = $query->paginate($perPage)->withQueryString();
        
        // Tarik daftar rombel dinamis hanya untuk periode yang sedang dipilih
        $rombelList = Rombel::where('periode_id', $selectedPeriodeId)->orderBy('name')->get();

        return view('bendahara.laporan.lap_transaksi', compact(
            'transaksi', 'rombelList', 'totalTransaksi', 'totalStor', 'totalTarik', 'periodeList', 'selectedPeriodeId'
        ));
    }

    /**
    * Menampilkan Data Laporan Pengajuan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_pengajuan(Request $request){
        $search        = $request->input('search');
        $rombel        = $request->input('rombel');
        $status        = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');
        $sortDate      = $request->input('sort_date', 'desc');
        $startDate     = $request->input('start_date');
        $endDate       = $request->input('end_date');
        $perPage       = $request->input('perPage', 10);

        $periodeList   = \App\Models\Periode::orderBy('tanggal_mulai', 'desc')->get();
        $periodeId     = $request->input('periode_id', PeriodeHelper::id());

        // Rombel yang tersedia mengikuti periode yang lagi dipilih
        $rombelList    = \App\Models\Rombel::where('periode_id', $periodeId)->orderBy('name')->get();

        $query = Pengajuan::with(['user.rombels.kelas', 'user.tabungan' => function ($q) use ($periodeId) {
                $q->where('periode_id', $periodeId);
            }])
            ->where('periode_id', $periodeId)
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombel, function ($q) use ($rombel, $periodeId) {
                $q->whereHas('user.rombels', function ($qq) use ($rombel, $periodeId) {
                    $qq->where('rombels.id', $rombel)
                    ->where('rombels.periode_id', $periodeId);
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($sortPenarikan, function ($q) use ($sortPenarikan) {
                $q->orderBy('jumlah_penarikan', $sortPenarikan);
            });

        if (!$sortPenarikan) {
            if (in_array($sortDate, ['asc', 'desc'])) {
                $query->orderBy('created_at', $sortDate);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        }

        $totalPengajuan = (clone $query)->count();
        $totalPending   = (clone $query)->where('status', 'Pending')->count();
        $totalDiterima  = (clone $query)->whereIn('status', ['Terima', 'Diterima', 'SUCCEEDED'])->count();
        $totalDitolak   = (clone $query)->whereIn('status', ['Tolak', 'tolak'])->count();

        $pengajuan = $query->paginate($perPage)->withQueryString();

        return view('bendahara.laporan.lap_pengajuan', compact(
            'pengajuan', 'rombelList', 'periodeList', 'periodeId',
            'totalPengajuan', 'totalPending', 'totalDiterima', 'totalDitolak'
        ));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi, Tabungan, dan Pengajuan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_export(Request $request)
    {
        $periodeId = PeriodeHelper::id();

        $siswas = User::where('roles_id', 4)
            ->whereHas('rombels', function ($q) use ($periodeId) {
                // Tambahkan nama tabel spesifik di sini (contoh: rombels.periode_id)
                $q->where('rombels.periode_id', $periodeId); 
            })
            ->orderBy('name')
            ->get();

        $rombels = Rombel::where('periode_id', $periodeId)
            ->orderBy('name')
            ->get();

        // Jangan lupa pastikan $periodes juga dikirim jika kamu memakai dropdown filter periode dari jawaban sebelumnya
        $periodes = \App\Models\Periode::orderBy('tanggal_mulai', 'desc')->get();

        return view('bendahara.laporan.export', compact('siswas', 'rombels', 'periodes'));
    }

    // Laporan Walikelas --------------------------------------------------------------------------------
    /**
    * Menampilkan Data Laporan Tabungan oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_walikelas_tabungan(Request $request)
    {
        $search    = $request->input('search');
        $rombelId  = $request->input('rombel');
        $sortSaldo = $request->input('sort_saldo');
        $perPage   = $request->input('perPage', 10);

        $selectedPeriodeId = $request->input('periode_id') ?: \App\Helpers\PeriodeHelper::id();
        $walikelasId = auth()->id();

        // 1. Ambil ID Rombel yang HANYA dipegang oleh Wali Kelas ini di periode terpilih
        $rombelIds = \App\Models\Rombel::where('walikelas_id', $walikelasId)
            ->where('periode_id', $selectedPeriodeId)
            ->pluck('id');

        // 2. Query Utama Siswa (Role 4)
        $query = \App\Models\User::where('roles_id', 4)
            ->whereHas('rombel', function ($q) use ($rombelIds, $selectedPeriodeId) {
                // Wajib tergabung dalam rombel milik wali kelas ini
                $q->whereIn('rombels.id', $rombelIds)
                  ->where('rombels.periode_id', $selectedPeriodeId);
            })
            ->with(['rombel' => function ($q) use ($selectedPeriodeId) {
                $q->where('rombels.periode_id', $selectedPeriodeId);
            }])
            // Jumlahkan kolom saldo di tabel tabungan menjadi atribut 'total_saldo'
            ->withSum(['tabungan as total_saldo' => function ($q) use ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            }], 'saldo');

        // 3. Pencarian (Nama, Username, atau Kode Absen di Pivot)
        if ($search) {
            $query->where(function ($q) use ($search, $selectedPeriodeId) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('rombel', function ($qq) use ($search, $selectedPeriodeId) {
                      $qq->where('rombels.periode_id', $selectedPeriodeId)
                         ->where('rombel_user.kode', 'like', "%{$search}%");
                  });
            });
        }

        // 4. Filter Dropdown Rombel Tertentu
        if ($rombelId && $rombelIds->contains($rombelId)) {
            $query->whereHas('rombel', function ($q) use ($rombelId, $selectedPeriodeId) {
                $q->where('rombels.id', $rombelId)
                  ->where('rombels.periode_id', $selectedPeriodeId);
            });
        }

        // 5. Sorting Saldo
        if (in_array($sortSaldo, ['asc', 'desc'])) {
            $query->orderBy('total_saldo', $sortSaldo);
        } else {
            $query->orderBy('name', 'asc');
        }

        // 6. Rekapitulasi Total (Menggunakan clone agar tidak merusak query pagination)
        $totalSiswa = (clone $query)->count();
        $totalSaldoKeseluruhan = (clone $query)->get()->sum('total_saldo');

        // 7. Eksekusi Pagination
        $user = $query->paginate($perPage)->withQueryString();

        // 8. Data Master untuk Filter Dropdown
        $rombelList = \App\Models\Rombel::whereIn('id', $rombelIds)->orderBy('name')->get();
        
        $periodeIdsMilikSaya = \App\Models\Rombel::where('walikelas_id', $walikelasId)->pluck('periode_id')->unique();
        $periodeList = \App\Models\Periode::whereIn('id', $periodeIdsMilikSaya)->orderBy('id', 'desc')->get();

        return view('walikelas.laporan.lap_tabungan', compact(
            'user', 'rombelList', 'periodeList', 'totalSiswa', 
            'totalSaldoKeseluruhan', 'selectedPeriodeId'
        ));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_walikelas_transaksi(Request $request)
    {
        $search         = $request->input('search');
        $rombelId       = $request->input('rombel');
        $tipeTransaksi  = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $sortDate       = $request->input('sort_date', 'desc');
        $startDate      = $request->input('start_date');
        $endDate        = $request->input('end_date');
        $perPage        = $request->input('perPage', 10);

        $periodeReq = $request->input('periode_id');
        $selectedPeriodeId = $periodeReq ?: PeriodeHelper::id();

        $rombelIds = Rombel::where('walikelas_id', auth()->id())
            ->where('periode_id', $selectedPeriodeId)
            ->pluck('id');

        $query = Transaksi::with([
                'user',
                'user.rombel' => function ($q) use ($selectedPeriodeId) {
                    $q->where('rombels.periode_id', $selectedPeriodeId);
                },
            ])
            ->where('periode_id', $selectedPeriodeId)
            ->where('status', 'success')
            // fix inti: transaksi yang muncul HARUS milik siswa yang ada di rombel walikelas ini
            ->whereHas('user.rombel', function ($q) use ($rombelIds, $selectedPeriodeId) {
                $q->whereIn('rombels.id', $rombelIds)
                ->where('rombels.periode_id', $selectedPeriodeId);
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($rombelId, function ($q) use ($rombelId, $rombelIds, $selectedPeriodeId) {
                if ($rombelIds->contains($rombelId)) {
                    $q->whereHas('user.rombel', function ($qq) use ($rombelId, $selectedPeriodeId) {
                        $qq->where('rombels.id', $rombelId)
                        ->where('rombels.periode_id', $selectedPeriodeId);
                    });
                }
            })
            ->when($tipeTransaksi, fn ($q) => $q->where('tipe_transaksi', $tipeTransaksi))
            ->when($tipePembayaran, fn ($q) => $q->where('pembayaran', $tipePembayaran))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at', $sortDate === 'asc' ? 'asc' : 'desc');

        $totalTransaksi = (clone $query)->count();
        $totalStor  = (clone $query)->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $totalTarik = (clone $query)->where('tipe_transaksi', 'tarik')->sum('jumlah_transaksi');

        $transaksi = $query->paginate($perPage)->withQueryString();

        $rombelList = Rombel::whereIn('id', $rombelIds)->orderBy('name')->get();

        $periodeIdsMilikSaya = Rombel::where('walikelas_id', auth()->id())->pluck('periode_id')->unique();
        $periodeList = Periode::whereIn('id', $periodeIdsMilikSaya)->orderBy('id', 'desc')->get();

        return view('walikelas.laporan.lap_transaksi', compact(
            'transaksi', 'rombelList', 'totalTransaksi', 'totalStor', 'totalTarik', 'periodeList', 'selectedPeriodeId'
        ));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi dan Tabungan oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_walikelas_export(Request $request)
    {
        $periodeReq = $request->input('periode_id');
        $periodeId = $periodeReq ?: \App\Helpers\PeriodeHelper::id();
        $walikelasId = auth()->id();

        // Cari rombel milik wali kelas ini pada periode yang dipilih
        $rombelIds = \App\Models\Rombel::where('walikelas_id', $walikelasId)
            ->where('periode_id', $periodeId)
            ->pluck('id');

        $siswas = collect();
        if ($rombelIds->isNotEmpty()) {
            $siswas = \App\Models\User::where('roles_id', 4)
                ->whereHas('rombel', function ($q) use ($rombelIds, $periodeId) {
                    $q->whereIn('rombels.id', $rombelIds)
                      ->where('rombels.periode_id', $periodeId);
                })
                ->orderBy('name')
                ->get();
        }

        $rombels = \App\Models\Rombel::whereIn('id', $rombelIds)->orderBy('name')->get();

        $periodeIdsMilikSaya = \App\Models\Rombel::where('walikelas_id', $walikelasId)->pluck('periode_id')->unique();
        $periodes = \App\Models\Periode::whereIn('id', $periodeIdsMilikSaya)->orderBy('tanggal_mulai', 'desc')->get();

        return view('walikelas.laporan.export', compact('siswas', 'rombels', 'periodes'));
    }

    // Laporan Siswa ------------------------------------------------------------------------------------
    /**
    * Menampilkan Data Laporan Tabungan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_siswa_tabungan(Request $request){
        $kelasId = auth()->user()->kelas->id;
        $perPage = request('perPage', 10);
        $user = User::where('roles_id', 4)->where('kelas_id', $kelasId)->paginate($perPage);
        $saldoTunai = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Tunai')->sum('jumlah_transaksi');
        $saldoDigital = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Digital')->sum('jumlah_transaksi');
        return view('siswa.laporan.lap_tabungan', compact('user', 'saldoTunai', 'saldoDigital'));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_siswa_transaksi(Request $request)
    {
        $tipeTransaksi  = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $sortTanggal    = $request->input('sort_tanggal', 'asc'); // asc = urutan buku tabungan (lama -> baru)
        $startDate      = $request->input('start_date');
        $endDate        = $request->input('end_date');
        $perPage        = $request->input('perPage', 10);

        $userId = auth()->id();

        // Daftar periode dimana siswa ini pernah punya tabungan, + selalu sertakan periode aktif
        // walau siswa belum punya row tabungan disitu (misal baru daftar)
        $periodeIds = Tabungan::where('user_id', $userId)
            ->pluck('periode_id')
            ->push(PeriodeHelper::id()) // pastikan periode aktif selalu muncul walau belum ada tabungan
            ->filter()
            ->unique();

        $periodeList = Periode::whereIn('id', $periodeIds)
            ->orderByDesc('tanggal_mulai')
            ->get();

        $selectedPeriodeId = $request->input('periode_id') ?? PeriodeHelper::id();

        $query = Transaksi::where('user_id', $userId)
            ->where('periode_id', $selectedPeriodeId)
            ->where('status', 'success')
            ->when($tipeTransaksi, fn ($q) => $q->where('tipe_transaksi', $tipeTransaksi))
            ->when($tipePembayaran, fn ($q) => $q->where('pembayaran', $tipePembayaran))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate));

        if (in_array($sortTanggal, ['asc', 'desc'])) {
            $query->orderBy('created_at', $sortTanggal);
        } else {
            $query->orderBy('created_at', 'asc');
        }

        // Summary sesuai filter aktif, dalam periode yang sedang dilihat
        $totalTransaksi = (clone $query)->count();
        $totalStor  = (clone $query)->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi');
        $totalTarik = (clone $query)->where('tipe_transaksi', 'tarik')->sum('jumlah_transaksi');

        // fix bug lama: dulu salah query kolom (tipe_transaksi bukan pembayaran).
        // sekarang hitung saldo bersih (stor - tarik) per metode pembayaran, dalam periode terpilih
        $saldoTunai = Transaksi::where('user_id', $userId)
                ->where('periode_id', $selectedPeriodeId)
                ->where('status', 'success')
                ->where('pembayaran', 'Tunai')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi')
            - Transaksi::where('user_id', $userId)
                ->where('periode_id', $selectedPeriodeId)
                ->where('status', 'success')
                ->where('pembayaran', 'Tunai')->where('tipe_transaksi', 'tarik')->sum('jumlah_transaksi');

        $saldoDigital = Transaksi::where('user_id', $userId)
                ->where('periode_id', $selectedPeriodeId)
                ->where('status', 'success')
                ->where('pembayaran', 'Digital')->where('tipe_transaksi', 'stor')->sum('jumlah_transaksi')
            - Transaksi::where('user_id', $userId)
                ->where('periode_id', $selectedPeriodeId)
                ->where('status', 'success')
                ->where('pembayaran', 'Digital')->where('tipe_transaksi', 'tarik')->sum('jumlah_transaksi');

        // Saldo tabungan saat ini (periode yang sedang dilihat)
        $tabunganPeriodeIni = Tabungan::where('user_id', $userId)
            ->where('periode_id', $selectedPeriodeId)
            ->first();

        // Info rombel siswa DI PERIODE YANG SEDANG DILIHAT (bukan rombel sekarang),
        // biar histori periode lama tetap nunjukin kelas yang benar waktu itu
        $rombelInfo = auth()->user()->rombels()
            ->wherePivot('periode_id', $selectedPeriodeId)
            ->first();

        $transaksi = $query->paginate($perPage)->withQueryString();

        return view('siswa.laporan.lap_transaksi', compact(
            'transaksi', 'periodeList', 'selectedPeriodeId',
            'totalTransaksi', 'totalStor', 'totalTarik',
            'saldoTunai', 'saldoDigital', 'tabunganPeriodeIni', 'rombelInfo'
        ));
    }

    /**
    * Menampilkan Data Laporan Pengajuan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_siswa_pengajuan(Request $request)
    {
        $status        = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');
        $sortDate      = $request->input('sort_date', 'desc');
        $startDate     = $request->input('start_date');
        $endDate       = $request->input('end_date');
        $perPage       = $request->input('perPage', 10);

        $userId = auth()->id();

        // Daftar periode dimana siswa ini pernah punya tabungan, + selalu sertakan periode aktif
        $periodeIds = Tabungan::where('user_id', $userId)
            ->pluck('periode_id')
            ->push(PeriodeHelper::id())
            ->filter()
            ->unique();

        $periodeList = Periode::whereIn('id', $periodeIds)
            ->orderByDesc('tanggal_mulai')
            ->get();

        $selectedPeriodeId = $request->input('periode_id') ?? PeriodeHelper::id();

        $query = Pengajuan::with(['user.tabungan' => function ($q) use ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            }])
            ->where('user_id', $userId)
            ->where('periode_id', $selectedPeriodeId)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate));

        if (in_array($sortPenarikan, ['asc', 'desc'])) {
            $query->orderBy('jumlah_penarikan', $sortPenarikan);
        } elseif (in_array($sortDate, ['asc', 'desc'])) {
            $query->orderBy('created_at', $sortDate);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Summary sesuai filter aktif, dalam periode yang sedang dilihat
        $totalPengajuan = (clone $query)->count();
        $totalPending   = (clone $query)->where('status', 'Pending')->count();
        $totalDiterima  = (clone $query)->whereIn('status', ['Terima', 'Diterima', 'SUCCEEDED'])->count();
        $totalDitolak   = (clone $query)->whereIn('status', ['Tolak', 'tolak'])->count();

        // Saldo tabungan siswa di periode yang sedang dilihat (bukan hasil query salah kolom seperti sebelumnya)
        $tabunganPeriodeIni = Tabungan::where('user_id', $userId)
            ->where('periode_id', $selectedPeriodeId)
            ->first();

        // Info rombel siswa di periode yang sedang dilihat
        $rombelInfo = auth()->user()->rombels()
            ->wherePivot('periode_id', $selectedPeriodeId)
            ->first();

        $pengajuan = $query->paginate($perPage)->withQueryString();

        return view('siswa.laporan.lap_pengajuan', compact(
            'pengajuan', 'periodeList', 'selectedPeriodeId',
            'totalPengajuan', 'totalPending', 'totalDiterima', 'totalDitolak',
            'tabunganPeriodeIni', 'rombelInfo'
        ));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi, Tabungan, dan Pengajuan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_siswa_export(Request $request)
    {
        $user = auth()->user();

        // Ambil semua periode yang relevan buat siswa ini:
        // - periode dimana dia pernah punya row tabungan
        // - selalu sertakan periode aktif, walau belum ada tabungan sama sekali di situ
        $periodeIds = Tabungan::where('user_id', $user->id)
            ->pluck('periode_id')
            ->push(PeriodeHelper::id())
            ->filter()
            ->unique();

        $periodes = Periode::whereIn('id', $periodeIds)
            ->orderByDesc('tanggal_mulai')
            ->get();

        return view('siswa.laporan.export', compact('user', 'periodes'));
    }
}
