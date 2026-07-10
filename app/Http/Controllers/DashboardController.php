<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tabungan;
use App\Models\Transaksi;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * DashboardController menghandle tampilan dashboard untuk berbagai role.
 */
class DashboardController extends Controller
{
    // Kepsek Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Kepala Sekolah.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function index(Request $request)
    {
        // 1. User yang paling sering transaksi (stor & success)
        $palingSeringData = Transaksi::select('user_id', DB::raw('COUNT(*) as total_transaksi'))
            ->where('tipe_transaksi', 'stor')
            ->where('status', 'success')
            ->groupBy('user_id')
            ->orderByDesc('total_transaksi')
            ->with('user') // relasi user() di model Transaksi
            ->first();

        if ($palingSeringData && $palingSeringData->user) {
            $palingSering = (object)[
                'nama'  => $palingSeringData->user->name,
                'value' => $palingSeringData->total_transaksi,
            ];
        } else {
            $palingSering = (object)[ 'nama' => null, 'value' => 0 ];
        }

        // 2. User dengan saldo tabungan terbesar (khusus roles_id = 4)
        $palingGedeData = Tabungan::select('tabungans.user_id', DB::raw('CAST(tabungans.saldo AS UNSIGNED) as saldo_numeric'))
            ->join('users', 'users.id', '=', 'tabungans.user_id')
            ->where('users.roles_id', 4)
            ->orderByDesc('saldo_numeric')
            ->with('user') // relasi user() di model Tabungan
            ->first();

        if ($palingGedeData && $palingGedeData->user) {
            $palingGede = (object)[
                'nama'  => $palingGedeData->user->name,
                'value' => $palingGedeData->saldo_numeric,
            ];
        } else {
            $palingGede = (object)[ 'nama' => null, 'value' => 0 ];
        }

        return view('welcome', compact('palingSering', 'palingGede'));
    }

    // Kepsek Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Kepala Sekolah.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function kepsek(){
        $jumlah_penabung = User::where('roles_id', 4)->where('status_siswa', 'aktif')->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');

        // Chart -----------------------------------------------------------------------------------------------
        $frekuensi = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('tipe_transaksi', 'stor')->where('periode_id',PeriodeHelper::id())
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        $total = Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "stor" THEN jumlah_transaksi WHEN tipe_transaksi = "tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->count();
        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->count();

        return view('kepsek.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'storKali',
            'tarikKali'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }

    // Bendahara Dasboard -----------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Role Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara(){
        $jumlah_penabung = User::where('roles_id', 4)->count();
        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Tunai')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Tunai')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')->where('status', 'success')->where('pembayaran', 'Digital')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi') - Transaksi::where('tipe_transaksi', 'Tarik')->where('status', 'success')->where('pembayaran', 'Digital')->where('periode_id',PeriodeHelper::id())->sum('jumlah_transaksi');
        $premi = Tabungan::where('periode_id',PeriodeHelper::id())->sum('premi');
        $bendahara = Tabungan::where('user_id', 2)->first();

        // Chart -----------------------------------------------------------------------------------------------
        $frekuensi = Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('tipe_transaksi', 'stor')->where('periode_id',PeriodeHelper::id())
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        $total = Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "stor" THEN jumlah_transaksi WHEN tipe_transaksi = "tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->count();
        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->count();

        return view('bendahara.index', compact(
            'jumlah_penabung',
            'transaksi_masuk',
            'transaksi_keluar',
            'jumlah_saldo_tunai',
            'jumlah_saldo_digital',
            'premi',
            'bendahara',
            'storKali',
            'tarikKali'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }

    // Walikelas Dashboard -----------------------------------------------------------------------------------------------
    /**
     * Menampilkan Dashboard Role Walikelas.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function walikelas()
    {
        $walikelas = auth()->user();
        $periodeId = \App\Helpers\PeriodeHelper::id();

        // Cari Rombel yang dipegang wali kelas ini pada periode aktif saat ini
        $rombel = \App\Models\Rombel::where('walikelas_id', $walikelas->id)
            ->where('periode_id', $periodeId)
            ->first();

        // Jika wali kelas belum diplot ke rombel manapun di periode ini, set default ID 0 biar gak crash
        $rombelId = $rombel ? $rombel->id : 0;

        // 1. Jumlah Penabung (Siswa dengan Role 4 yang terdaftar di rombel aktif milik wali kelas)
        $jumlah_penabung = \App\Models\User::where('roles_id', 4)
            ->whereHas('rombel', function ($query) use ($rombelId, $periodeId) {
                $query->where('rombels.id', $rombelId)->where('rombels.periode_id', $periodeId);
            })
            ->count();

        // 2. Akumulasi Transaksi Masuk (Stor) di Rombel ini
        $transaksi_masuk = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })
            ->sum('jumlah_transaksi');

        // 3. Akumulasi Transaksi Keluar (Tarik) di Rombel ini
        $transaksi_keluar = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })
            ->sum('jumlah_transaksi');

        // 4. Hitung Saldo Bersih Tunai Rombel (Stor - Tarik)
        $jumlah_saldo_tunai = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('pembayaran', 'Tunai')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })->sum('jumlah_transaksi') 
            - 
            \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Tarik')
            ->where('pembayaran', 'Tunai')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })->sum('jumlah_transaksi');

        // 5. Hitung Saldo Bersih Digital Rombel (Stor - Tarik)
        $jumlah_saldo_digital = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('pembayaran', 'Digital')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })->sum('jumlah_transaksi') 
            - 
            \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Tarik')
            ->where('pembayaran', 'Digital')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })->sum('jumlah_transaksi');

        // 6. Frekuensi Setor Grafik (Ubah 'stor' jadi 'Stor' sesuai standard db)
        $frekuensi = \App\Models\Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        // 7. Akumulasi Total Saldo Grafik harian
        $total = \App\Models\Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "Stor" THEN jumlah_transaksi WHEN tipe_transaksi = "Tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('periode_id', $periodeId)
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        // 8. Berapa kali transaksi dilakukan di rombel ini
        $storKali = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })->count();

        $tarikKali = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            })->count();

        return view('walikelas.index', compact(
            'jumlah_penabung', 'transaksi_masuk', 'transaksi_keluar',
            'jumlah_saldo_tunai', 'jumlah_saldo_digital', 'storKali', 'tarikKali'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }

    // Siswa Dashboard -----------------------------------------------------------------------------------------------
    /**
     * Menampilkan Dashboard Role Siswa.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function siswa()
    {
        $siswa = auth()->user();
        $periodeId = \App\Helpers\PeriodeHelper::id();

        // 1. Ambil jumlah saldo akumulasi dari tabel tabungans untuk periode ini
        $jumlah_saldo = \App\Models\Tabungan::where('user_id', $siswa->id)
            ->where('periode_id', $periodeId)
            ->sum('saldo');

        // 2. Akumulasi uang masuk (Stor) siswa ini di periode aktif
        $transaksi_masuk = \App\Models\Transaksi::where('user_id', $siswa->id)
            ->where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        // 3. Akumulasi uang keluar (Tarik) siswa ini di periode aktif
        $transaksi_keluar = \App\Models\Transaksi::where('user_id', $siswa->id)
            ->where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        // 4. Saldo Bersih Tunai & Digital per siswa di periode aktif
        $jumlah_saldo_tunai = \App\Models\Transaksi::where('user_id', $siswa->id)->where('periode_id', $periodeId)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Tunai')->where('status', 'success')->sum('jumlah_transaksi') 
            - \App\Models\Transaksi::where('user_id', $siswa->id)->where('periode_id', $periodeId)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Tunai')->where('status', 'success')->sum('jumlah_transaksi');
        
        $jumlah_saldo_digital = \App\Models\Transaksi::where('user_id', $siswa->id)->where('periode_id', $periodeId)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Digital')->where('status', 'success')->sum('jumlah_transaksi') 
            - \App\Models\Transaksi::where('user_id', $siswa->id)->where('periode_id', $periodeId)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Digital')->where('status', 'success')->sum('jumlah_transaksi');

        // 5. Frekuensi transaksi harian siswa untuk grafik
        $frekuensi = \App\Models\Transaksi::selectRaw('DATE(created_at) as date, SUM(jumlah_transaksi) as total')
            ->where('user_id', $siswa->id)
            ->where('periode_id', $periodeId)
            ->where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->total
                ];
            });

        // 6. Grafik akumulasi pertumbuhan tabungan siswa
        $total = \App\Models\Transaksi::selectRaw('DATE(created_at) as date, SUM(CASE WHEN tipe_transaksi = "Stor" THEN jumlah_transaksi WHEN tipe_transaksi = "Tarik" THEN -jumlah_transaksi ELSE 0 END) as daily_total')
            ->where('user_id', $siswa->id)
            ->where('periode_id', $periodeId)
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->reduce(function ($carry, $item) {
                $lastTotal = $carry->isNotEmpty() ? $carry->last()['y'] : 0;
                $carry->push([
                    'x' => $item->date,
                    'y' => (string) ($lastTotal + $item->daily_total)
                ]);
                return $carry;
            }, collect());

        // 7. Hitung intensitas kuantitas mutasi (Berapa kali)
        $storKali = \App\Models\Transaksi::where('user_id', $siswa->id)->where('periode_id', $periodeId)->where('tipe_transaksi', 'Stor')->where('status', 'success')->count();
        $tarikKali = \App\Models\Transaksi::where('user_id', $siswa->id)->where('periode_id', $periodeId)->where('tipe_transaksi', 'Tarik')->where('status', 'success')->count();

        // 8. Load data tabel riwayat mutasi di dashboard bawah (Hanya memuat transaksi periode aktif)
        $perPage = request('perPage', 10);
        $transaksi = \App\Models\Transaksi::where('user_id', $siswa->id)
            ->where('periode_id', $periodeId)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Set variabel dummy jumlah penabung bernilai 1 karena ini dashboard personal milik siswa sendiri
        $jumlah_penabung = 1;

        return view('siswa.index', compact(
            'jumlah_penabung', 'transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo',
            'jumlah_saldo_tunai', 'jumlah_saldo_digital', 'transaksi', 'storKali', 'tarikKali'
        ))->with([
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'chart_frekuensi' => $frekuensi,
            'chart_total' => $total,
        ]);
    }
}