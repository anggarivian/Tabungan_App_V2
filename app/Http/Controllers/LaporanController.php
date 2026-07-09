<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Periode;
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
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $sortSaldo = $request->input('sort_saldo');
        $perPage = request('perPage', 10);

        $user = User::with(['tabungan', 'kelas'])
            ->whereHas('tabungan')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhereHas('tabungan', function ($q) use ($search) {
                        $q->where('id', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->withSum('tabungan as total_saldo', 'saldo');

        if ($sortSaldo === 'asc' || $sortSaldo === 'desc') {
            $user->orderBy('total_saldo', $sortSaldo);
        }

        $user = $user->paginate($perPage);
        $kelasList = Kelas::orderBy('name')->get();
        return view('bendahara.laporan.lap_tabungan', compact('user', 'kelasList'));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_transaksi(Request $request) {
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $tipeTransaksi = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $sortTanggal = $request->input('sort_tanggal');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $perPage = request('perPage', 10);

        $transaksi = Transaksi::with(['user.kelas'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                      ->orWhere('username', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('user.kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->when($tipeTransaksi, function ($query) use ($tipeTransaksi) {
                $query->where('tipe_transaksi', $tipeTransaksi);
            })
            ->when($tipePembayaran, function ($query) use ($tipePembayaran) {
                $query->where('pembayaran', $tipePembayaran);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($sortTanggal, function ($query) use ($sortTanggal) {
                $query->orderBy('created_at', $sortTanggal);
            })
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->all());

        $kelasList = Kelas::orderBy('name')->get();
        // 1) Ambil daftar kelas
            $classes = Kelas::orderBy('id')->get();

            // 2) Query: join ke users, group by DATE(created_at) & kelas_id,
            //    lalu SUM jumlah_transaksi
            $aggregates = Transaksi::join('users', 'transaksis.user_id', '=', 'users.id')
                ->select([
                    DB::raw('DATE(transaksis.created_at) as tanggal'),
                    'users.kelas_id',
                    DB::raw('SUM(transaksis.jumlah_transaksi) as total_per_kelas'),
                ])
                ->groupBy('tanggal', 'users.kelas_id')
                ->orderBy('tanggal')
                ->get();

            // 3) Kumpulkan semua tanggal unik
            $dates = $aggregates->pluck('tanggal')->unique();

            // 4) Bangun array pivot per tanggal
            $rows = $dates->map(function($date) use ($classes, $aggregates) {
                // inisialisasi semua kelas ke 0
                $perKelas = $classes
                    ->pluck('id')
                    ->mapWithKeys(fn($id) => [(string)$id => 0])
                    ->toArray();

                $totalAll = 0;

                // isi nilai dari hasil query
                $aggregates
                    ->where('tanggal', $date)
                    ->each(function($item) use (&$perKelas, &$totalAll) {
                        $key = (string)$item->kelas_id;
                        $perKelas[$key] = (int) $item->total_per_kelas;
                        $totalAll += (int) $item->total_per_kelas;
                    });

                return [
                    'tanggal'  => $date,
                    'perKelas' => $perKelas,
                    'total'    => $totalAll,
                ];
            })
            ->values()
            ->toArray();

        return view('bendahara.laporan.lap_transaksi', compact('transaksi', 'kelasList', 'rows', 'classes'));
    }

    /**
    * Menampilkan Data Laporan Pengajuan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_pengajuan(Request $request){
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $status = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');
        $sortDate = $request->input('sort_date');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $perPage = request('perPage', 10);

        $pengajuan = Pengajuan::with(['user', 'user.kelas'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%');
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('user.kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($sortPenarikan, function ($query) use ($sortPenarikan) {
                $query->orderBy('jumlah_penarikan', $sortPenarikan);
            })
            ->when($sortDate, function ($query) use ($sortDate) {
                $query->orderBy('created_at', $sortDate);
            })
            ->paginate($perPage)
            ->withQueryString();

        $kelasList = Kelas::orderBy('name')->get();
        return view('bendahara.laporan.lap_pengajuan', compact('pengajuan', 'kelasList'));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi, Tabungan, dan Pengajuan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_bendahara_export(Request $request)
    {
        $siswas = User::where('roles_id', 4)->get();
        $kelas = Kelas::all();

        return view('bendahara.laporan.export', compact('siswas', 'kelas'));
    }

    // Laporan Walikelas --------------------------------------------------------------------------------
    /**
    * Menampilkan Data Laporan Tabungan oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_walikelas_tabungan(Request $request){
        $kelasId = auth()->user()->kelas->id;
        $kelas = auth()->user()->kelas->name;
        $perPage = request('perPage', 10);

        $search = $request->input('search');
        $sortSaldo = $request->input('sort_saldo');
        $tipeTransaksi = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');

        $user = User::with(['tabungan', 'kelas'])
            ->where('roles_id', 4)
            ->where('kelas_id', $kelasId)
            ->whereHas('tabungan')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%')
                      ->orWhereHas('tabungan', function ($q) use ($search) {
                          $q->where('id', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($tipeTransaksi, function ($query) use ($tipeTransaksi) {
                $query->where('tipe_transaksi', $tipeTransaksi);
            })
            ->when($tipePembayaran, function ($query) use ($tipePembayaran) {
                $query->where('pembayaran', $tipePembayaran);
            })
            ->withSum('tabungan as total_saldo', 'saldo');

        if (in_array($sortSaldo, ['asc', 'desc'])) {
            $user->orderBy('total_saldo', $sortSaldo);
        }

        $user = $user->paginate($perPage)->withQueryString();

        return view('walikelas.laporan.lap_tabungan', compact('user', 'kelas'));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_walikelas_transaksi(Request $request){
        $kelasId = auth()->user()->kelas->id;
        $kelas = auth()->user()->kelas->name;
        $perPage = request('perPage', 10);

        $search = $request->input('search');
        $tipeTransaksi = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sortDate = $request->input('sort_date'); // Tambahkan untuk sorting tanggal transaksi

        $transaksi = Transaksi::with(['user.kelas'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('username', 'like', '%' . $search . '%');
                });
            })
            ->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('user.kelas', function ($q) use ($kelas) {
                    $q->where('name', $kelas);
                });
            })
            ->when($tipeTransaksi, function ($query) use ($tipeTransaksi) {
                $query->where('tipe_transaksi', $tipeTransaksi);
            })
            ->when($tipePembayaran, function ($query) use ($tipePembayaran) {
                $query->where('pembayaran', $tipePembayaran);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($sortDate, function ($query) use ($sortDate) {
                $query->orderBy('created_at', $sortDate);
            })
            ->where('status', 'success')
            ->paginate($perPage)
            ->withQueryString();

        return view('walikelas.laporan.lap_transaksi', compact('transaksi', 'kelas'));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi dan Tabungan oleh Walikelas.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_walikelas_export(Request $request)
    {
        $siswas = User::where('kelas_id', auth()->user()->kelas->id )->where('roles_id', 4)->get();
        $kelas = Kelas::all();

        return view('walikelas.laporan.export', compact('siswas', 'kelas'));
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

    public function lap_siswa_transaksi(Request $request){
        $tipeTransaksi = $request->input('tipe_transaksi');
        $tipePembayaran = $request->input('tipe_pembayaran');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = request('perPage', 10);

        $transaksi = Transaksi::with(['user.kelas'])
            ->whereHas('user', function ($query) {
                $query->where('id', auth()->id());
            })
            ->when($tipeTransaksi, function ($query) use ($tipeTransaksi) {
                $query->where('tipe_transaksi', $tipeTransaksi);
            })
            ->when($tipePembayaran, function ($query) use ($tipePembayaran) {
                $query->where('pembayaran', $tipePembayaran);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', 'success')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        $saldoTunai = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Tunai')->sum('jumlah_transaksi');
        $saldoDigital = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Digital')->sum('jumlah_transaksi');

        return view('siswa.laporan.lap_transaksi', compact('transaksi', 'saldoTunai', 'saldoDigital'));
    }

    /**
    * Menampilkan Data Laporan Pengajuan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_siswa_pengajuan(Request $request){
        $status = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $perPage = request('perPage', 10);

        $pengajuan = Pengajuan::with(['user.kelas'])
            ->whereHas('user', function ($query) {
                $query->where('id', auth()->id());  // Filter data berdasarkan user yang login
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($sortPenarikan, function ($query) use ($sortPenarikan) {
                $query->orderBy('jumlah_penarikan', $sortPenarikan);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->paginate($perPage);

        $saldoTunai = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Tunai')->sum('jumlah_transaksi');
        $saldoDigital = Transaksi::where('user_id', auth()->id())->where('tipe_transaksi', 'Digital')->sum('jumlah_transaksi');

        return view('siswa.laporan.lap_pengajuan', compact('pengajuan', 'saldoTunai', 'saldoDigital'));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi, Tabungan, dan Pengajuan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_siswa_export(Request $request)
    {
        $siswas = auth()->user();
        $kelas = Kelas::all();

        return view('siswa.laporan.export', compact('siswas', 'kelas'));
    }
}
