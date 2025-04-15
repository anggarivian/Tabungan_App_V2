<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Pengajuan;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Laporan Kepsek ----------------------------------------------------------------------------------
    /**
    * Menampilkan Data Laporan Tabungan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_tabungan(Request $request){
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
        return view('kepsek.laporan.lap_tabungan', compact('user', 'kelasList'));
    }

    /**
    * Menampilkan Data Laporan Transaksi oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_transaksi(Request $request) {
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
            ->paginate($perPage)
            ->appends($request->all());

        $kelasList = Kelas::orderBy('name')->get();
        return view('kepsek.laporan.lap_transaksi', compact('transaksi', 'kelasList'));
    }

    /**
    * Menampilkan Data Laporan Pengajuan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_pengajuan(Request $request){
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $status = $request->input('status');
        $sortPenarikan = $request->input('sort_penarikan');
        $sortDate = $request->input('sort_date'); // Tambahkan ini
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
        return view('kepsek.laporan.lap_pengajuan', compact('pengajuan', 'kelasList'));
    }

    /**
    * Menampilkan Halaman Export Laporan Transaksi, Tabungan, dan Pengajuan oleh Kepala Sekolah.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function lap_kepsek_export(Request $request)
    {
        $siswas = User::where('roles_id', 4)->get();
        $kelas = Kelas::all();

        return view('kepsek.laporan.export', compact('siswas', 'kelas'));
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
        return view('bendahara.laporan.lap_transaksi', compact('transaksi', 'kelasList'));
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
        $siswas = User::where('kelas_id', auth()->user()->kelas->id )->get();
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
        return view('siswa.laporan.lap_tabungan', compact('user'));
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

        return view('siswa.laporan.lap_transaksi', compact('transaksi'));
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

        return view('siswa.laporan.lap_pengajuan', compact('pengajuan'));
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
