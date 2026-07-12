<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Xendit\Xendit;
use Xendit\Invoice;
use App\Models\User;
use Xendit\Invoices;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Periode;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use Xendit\Configuration;
use Midtrans\Notification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\RupiahHelper;
use App\Helpers\PeriodeHelper;
use Xendit\Invoice\InvoiceApi;
use App\Mail\TabunganTarikMail;
use App\Mail\TabunganStoredMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Xendit\Invoice\CreateInvoiceRequest;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWhatsAppMessage;


class TabunganController extends Controller
{
    /**
    * Konstruktor untuk menginisialisasi konfigurasi API Xendit.
    */
    private $invoiceClient;

    public function __construct()
    {
        Configuration::setXenditKey(config('services.xendit.api_key'));
    }

    // Bendahara ------------------------------------------------------------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Tabungan oleh Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_index()
{
    $periodeId = PeriodeHelper::id();
    $periodeAktif = Periode::find($periodeId);

    // fix: guard kalau belum ada periode aktif sama sekali
    if (!$periodeId) {
        return view('bendahara.tabungan.index', [
            'periodeAktif'          => null,
            'transaksi_masuk'       => 0,
            'transaksi_keluar'      => 0,
            'jumlah_saldo_tunai'    => 0,
            'jumlah_saldo_digital'  => 0,
            'rombelList'            => [],
            'rombels'               => collect(),
            'storKali'              => 0,
            'tarikKali'             => 0,
            'rekapRombel'           => [], // fix: default kosong biar blade nggak error
        ]);
    }

    // fix: tambahkan periode_id eksplisit, jangan cuma andalkan whereDate(created_at, today())
    $baseGlobalQuery = Transaksi::where('periode_id', $periodeId)
        ->whereDate('created_at', Carbon::today())
        ->where('status', 'success');

    $transaksi_masuk  = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
    $transaksi_keluar = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');

    $storKali  = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->count();
    $tarikKali = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->count();

    $jumlah_saldo_tunai = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi')
                        - (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');

    $jumlah_saldo_digital = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Digital')->sum('jumlah_transaksi')
                        - (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

    $rombels = Rombel::where('periode_id', $periodeId)->orderBy('name')->get();
    $rombelList = [];

    foreach ($rombels as $rombel) {
        // fix: filter periode_id di query Transaksi itu sendiri, sekaligus dobel-cek di whereHas
        $baseRombelQuery = Transaksi::where('periode_id', $periodeId)
            ->whereHas('user.rombel', function ($q) use ($rombel, $periodeId) {
                $q->where('rombels.id', $rombel->id)
                ->where('rombels.periode_id', $periodeId);
            })
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'success');

        $transaksi_hari_ini = (clone $baseRombelQuery)
            ->with('user') // fix: eager load, cegah N+1 di blade
            ->orderBy('created_at', 'desc')
            ->get();

        $stor_rombel  = (clone $baseRombelQuery)->where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
        $tarik_rombel = (clone $baseRombelQuery)->where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');

        $rombelList[$rombel->id] = [
            'nama_rombel' => $rombel->name,
            'data'  => $transaksi_hari_ini,
            'stor'  => $stor_rombel,
            'tarik' => $tarik_rombel,
        ];
    }

    // fix: pindahkan logic rekap dari getRekapRombelData() kesini, langsung dihitung server-side
    $transaksiRekap = DB::table('transaksis')
        ->join('rombel_user', 'transaksis.user_id', '=', 'rombel_user.user_id')
        ->join('rombels', 'rombel_user.rombel_id', '=', 'rombels.id')
        ->where('transaksis.periode_id', $periodeId)
        ->where('rombels.periode_id', $periodeId) // pastikan rombelnya di periode yg sama
        ->where('transaksis.status', 'success')
        ->where('transaksis.tipe_transaksi', 'Stor')
        ->select(
            DB::raw('DATE(transaksis.created_at) as tanggal'),
            'rombels.id as rombel_id',
            DB::raw('SUM(transaksis.jumlah_transaksi) as total')
        )
        ->groupBy(DB::raw('DATE(transaksis.created_at)'), 'rombels.id')
        ->orderBy(DB::raw('DATE(transaksis.created_at)'), 'desc')
        ->get();

    // Susun ulang jadi format pivot per tanggal (reuse $rombels yg sudah diambil di atas)
    $rekapRombel = [];
    foreach ($transaksiRekap as $t) {
        $tgl = $t->tanggal;

        if (!isset($rekapRombel[$tgl])) {
            $rekapRombel[$tgl] = ['tanggal' => $tgl, 'total_semua' => 0];
            foreach ($rombels as $r) {
                $rekapRombel[$tgl]['rombel_' . $r->id] = 0;
            }
        }

        $rekapRombel[$tgl]['rombel_' . $t->rombel_id] = (int) $t->total;
        $rekapRombel[$tgl]['total_semua'] += (int) $t->total;
    }
    $rekapRombel = array_values($rekapRombel);

    return view('bendahara.tabungan.index', compact(
        'periodeAktif',
        'transaksi_masuk',
        'transaksi_keluar',
        'jumlah_saldo_tunai',
        'jumlah_saldo_digital',
        'rombelList',
        'rombels',
        'storKali',
        'tarikKali',
        'rekapRombel'
    ));
}

    /**
    * Import Data Transaksi Tabungan oleh Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_importTransaksi(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new TransaksiImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data transaksi berhasil diimport!');
    }

    /**
    * Menampilkan Dashboard Stor Tabungan oleh Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_stor()
    {
        return view('bendahara.tabungan.stor');
    }

    /**
    * Menampilkan Dashboard Stor Tabungan Massal / Per Rombel oleh Bendahara.
    *
    * @param $id ID Rombel
    * @return \Illuminate\Http\RedirectResponse
    */

    // Tampilkan form stor masal
    public function bendahara_storMasal($id)
    {
        $periodeId = PeriodeHelper::id();

        if (!$periodeId) {
            abort(400, 'Tidak ada periode aktif.');
        }

        // fix: pastikan rombel yang diakses memang ada DI PERIODE AKTIF,
        // bukan sekadar findOrFail(id) tanpa peduli periode
        $rombel = Rombel::with('walikelas')
            ->where('id', $id)
            ->where('periode_id', $periodeId)
            ->firstOrFail();

        $siswa = User::where('roles_id', 4)
            ->whereHas('rombel', function ($q) use ($id, $periodeId) {
                $q->where('rombels.id', $id)
                ->where('rombels.periode_id', $periodeId); // fix: dobel-cek periode juga di sini
            })
            ->with([
                'rombel' => function ($q) use ($id, $periodeId) {
                    $q->where('rombels.id', $id)
                    ->where('rombels.periode_id', $periodeId);
                },
                'tabungan' => function ($q) use ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            ])
            ->get()
            ->sortBy(function ($user) {
                return $user->rombel->first()->pivot->kode ?? $user->name;
            });

        $kemarin = Carbon::yesterday()->toDateString();
        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->where('periode_id', $periodeId) // fix: konsisten filter periode
            ->where('tipe_transaksi', 'Stor')
            ->whereHas('user.rombel', function ($q) use ($id, $periodeId) {
                $q->where('rombels.id', $id)
                ->where('rombels.periode_id', $periodeId);
            })
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        return view('bendahara.tabungan.stor_masal', compact(
            'rombel', 'siswa', 'jumlahTransaksiKemarin'
        ));
    }

    /**
    * Menampilkan Dashboard Tarik Tabungan oleh Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_tarik()
    {
        return view('bendahara.tabungan.tarik');
    }

    /**
    * Mencari data tabungan siswa berdasarkan username yang diberikan.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\JsonResponse
    */
    
    public function bendahara_search(Request $request)
    {
        $kode = strtoupper(trim($request->get('kode')));
        $periodeId = PeriodeHelper::id();

        $user = User::findByKode($kode, $periodeId);

        if ($user) {
            // ambil rombel periode aktif langsung dari relasi many-to-many
            $rombelSekarang = $user->rombel()
                ->where('rombels.periode_id', $periodeId)
                ->first();

            // fix: query eksplisit ke tabel Tabungan, jangan gantung ke $user->tabungan
            // (aman walau relasi di model berubah jadi hasMany)
            $tabungan = Tabungan::where('user_id', $user->id)
                ->where('periode_id', $periodeId)
                ->first();

            $saldo = $tabungan->saldo ?? 0;

            $riwayat = Transaksi::where('user_id', $user->id)
                ->where('periode_id', $periodeId)
                ->where('status', 'success')
                ->orderBy('created_at', 'asc')
                ->limit(20)
                ->get()
                ->map(function ($t) {
                    return [
                        'tanggal'     => $t->created_at->format('d M, H:i'),
                        'tipe'        => strtolower($t->tipe_transaksi),
                        'jumlah'      => number_format($t->jumlah_transaksi, 0, ',', '.'),
                        'saldo_akhir' => number_format($t->saldo_akhir, 0, ',', '.'),
                        'pembayaran'  => $t->pembayaran,
                    ];
                });

            return response()->json([
                'name'     => $user->name,
                'rombel'   => $rombelSekarang->name ?? 'Tidak Ada',
                'tabungan' => number_format($saldo, 0, ',', '.'),
                'riwayat'  => $riwayat,
            ]);
        }

        return response()->json([
            'name'     => 'Tidak Ada',
            'rombel'   => 'Tidak Ada',
            'tabungan' => 'Tidak Ada',
            'riwayat'  => [],
        ]);
    }

    /**
    * Menyimpan stor tabungan siswa oleh Bendahara.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_storTabungan(Request $request)
    {
        $request->merge([
            'jumlah_stor' => str_replace('.', '', $request->jumlah_stor),
        ]);

        $validated = $request->validate([
            'kode'         => 'required',
            'name'         => 'required|not_in:Tidak Ada',
            'rombel'       => 'required',
            'jumlah_stor'  => 'required|numeric|min:1000',
        ], [
            'kode.required'        => 'Kode Siswa harus diisi.',
            'name.required'        => 'Nama harus diisi.',
            'name.not_in'          => 'Kode Siswa tidak ditemukan.',
            'rombel.required'      => 'Rombel harus diisi.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric'  => 'Jumlah stor harus berupa angka.',
            'jumlah_stor.min'      => 'Minimal stor adalah Rp 1.000.',
        ]);

        $periodeId = PeriodeHelper::id();
        $user = User::findByKode($validated['kode'], $periodeId);

        if (!$user) {
            return back()->withErrors(['kode' => 'Kode siswa tidak ditemukan di periode aktif.']);
        }

        // Cari tabungan, jika sistem gagal membuat di awal, kita buatkan sebagai fallback
        $tabungan = Tabungan::firstOrCreate(
            ['user_id' => $user->id, 'periode_id' => $periodeId],
            ['saldo' => 0, 'premi' => 0, 'sisa' => 0]
        );

        $awal = $tabungan->saldo;
        $stor = $validated['jumlah_stor'];
        $akhir = $awal + $stor;

        // Asumsi perhitungan premi sesuai request awal lu
        $premi = $akhir * 0.025;
        $sisa  = $akhir - $premi;

        $transaksi = new Transaksi([
            'jumlah_transaksi' => $stor,
            'saldo_awal'       => $awal,
            'saldo_akhir'      => $akhir,
            'tipe_transaksi'   => 'Stor',
            'pembayaran'       => 'Tunai',
            'status'           => 'success',
            'pembuat'          => auth()->user()->name,
            'token_stor'       => Str::random(10),
            'periode_id'       => $periodeId,
        ]);
        $transaksi->user()->associate($user);
        $transaksi->tabungan()->associate($tabungan);
        $transaksi->save();

        $tabungan->update([
            'saldo' => $akhir,
            'premi' => $premi,
            'sisa'  => $sisa,
        ]);

        // // Cek class sebelum dispatch menghindari error jika Job belum dibuat
        // if(class_exists('\App\Jobs\SendWhatsAppMessage')) {
        //     \App\Jobs\SendWhatsAppMessage::dispatch($transaksi);
        // }

        return redirect()->back()
            ->with('success', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data stor tabungan atas nama '.$user->name.' berhasil disimpan.')
            ->with('alert-duration', 3000);
    }


    
    public function bendahara_batalTransaksi(Request $request, $id)
    {
        // 1. Proteksi konfirmasi (wajib ngetik BATAL)
        $request->validate([
            'konfirmasi' => 'required|in:BATAL,batal',
        ], [
            'konfirmasi.required' => 'Kolom konfirmasi harus diisi.',
            'konfirmasi.in'       => 'Ketik kata BATAL dengan benar untuk membatalkan.',
        ]);

        $transaksi = \App\Models\Transaksi::findOrFail($id);

        // 2. Cek apakah transaksi sudah pernah dibatalkan biar nggak double
        if ($transaksi->status != 'success') {
            return back()->withErrors(['batal' => 'Transaksi ini sudah dibatalkan sebelumnya.']);
        }

        $tabungan = \App\Models\Tabungan::findOrFail($transaksi->tabungan_id);

        // 3. Logika Reversal (Putar Balik Saldo)
        if ($transaksi->tipe_transaksi == 'Stor') {
            // Kalau batal stor, kurangi saldo siswa
            $tabungan->saldo -= $transaksi->jumlah_transaksi;
            
        } elseif ($transaksi->tipe_transaksi == 'Tarik') {
            // Kalau batal tarik, kembalikan saldo utuh ke siswa (Tarik + Admin)
            $tabungan->saldo += $transaksi->jumlah_transaksi;

            // --- REVERSE ENGINEERING PREMI BENDAHARA ---
            // Karena jumlah_transaksi = Nilai Tarik + Admin (5%),
            // Kita hitung mundur untuk mencari nilai premi yang dulu dipotong.
            $estimasiTarik = $transaksi->jumlah_transaksi / 1.05;
            $premiBatal    = ceil(($estimasiTarik * 0.05) / 1000) * 1000;

            // Tarik balik (kurangi) saldo dari akun Bendahara (ID 2)
            $bendaharaTabungan = \App\Models\Tabungan::where('user_id', 2)
                                    ->where('periode_id', $transaksi->periode_id)
                                    ->first();
                                    
            if ($bendaharaTabungan) {
                $bendaharaTabungan->decrement('saldo', $premiBatal);
            }
        }

        
        // 4. Hitung ulang estimasi premi keseluruhan tabungan siswa
        $tabungan->premi = $tabungan->saldo * 0.025;
        $tabungan->sisa  = $tabungan->saldo - $tabungan->premi;
        $tabungan->save();

        // 5. Ubah status transaksi menjadi batal
        $transaksi->status = 'batal';
        $transaksi->save();

        return redirect()->back()
            ->with('success', 'Transaksi berhasil dibatalkan.')
            ->with('alert-type', 'warning')
            ->with('alert-message', 'Transaksi ' . $transaksi->tipe_transaksi . ' sebesar Rp ' . number_format($transaksi->jumlah_transaksi, 0, ',', '.') . ' dibatalkan. Saldo siswa dan kas admin telah disesuaikan.')
            ->with('alert-duration', 4000);
    }

    /**
    * Menyimpan stor tabungan siswa secara masal / perkelas oleh Bendahara.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_storMasalTabungan(Request $request)
    {
        $inputs = $request->input('input', []);
        $periodeId = \App\Helpers\PeriodeHelper::id();

        // 1. Sanitasi & Filter Input (Hanya ambil yang kolom stor-nya diisi >= 1000)
        $sanitized = [];
        foreach ($inputs as $row) {
            // Hilangkan titik ribuan
            $stor = preg_replace('/\D+/', '', $row['stor'] ?? '');
            
            if (!empty($row['kode']) && is_numeric($stor) && $stor >= 1000) {
                $sanitized[] = [
                    'kode' => $row['kode'],
                    'stor' => (int) $stor,
                ];
            }
        }

        if (empty($sanitized)) {
            return redirect()->back()->withErrors(['Data kosong atau tidak valid. Pastikan minimal stor adalah Rp 1.000.']);
        }

        $transaksis = []; // Array penampung untuk notif WA

        // 2. Bungkus proses database di dalam Transaction (Biar kalau error 1, batal semua)
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($sanitized as $data) {
                // Cari user berdasarkan kode di pivot (menggunakan helper Model lu)
                $user = \App\Models\User::findByKode($data['kode'], $periodeId);
                if (!$user) continue;

                // Pastikan dompet tabungan ada, kalau belum ada, buatkan
                $tabungan = \App\Models\Tabungan::firstOrCreate(
                    ['user_id' => $user->id, 'periode_id' => $periodeId],
                    ['saldo' => 0, 'premi' => 0, 'sisa' => 0]
                );

                // Lock row tabungan ini biar gak bentrok kalau ada yang input barengan
                $tabungan = \App\Models\Tabungan::where('id', $tabungan->id)->lockForUpdate()->first();

                $stor  = $data['stor'];
                $awal  = $tabungan->saldo;
                $akhir = $awal + $stor;

                // Hitung premi/tabungan wajib 2.5% yang mengendap (hak siswa)
                $premiSiswa = $akhir * 0.025;
                $sisa       = $akhir - $premiSiswa;

                $tabungan->update([
                    'saldo' => $akhir,
                    'premi' => $premiSiswa,
                    'sisa'  => $sisa,
                ]);

                // Catat Transaksi
                $trx = new \App\Models\Transaksi([
                    'jumlah_transaksi' => $stor,
                    'saldo_awal'       => $awal,
                    'saldo_akhir'      => $akhir,
                    'tipe_transaksi'   => 'Stor',
                    'pembayaran'       => 'Tunai',
                    'status'           => 'success',
                    'pembuat'          => auth()->user()->name,
                    'token_stor'       => \Illuminate\Support\Str::random(10),
                    'periode_id'       => $periodeId,
                ]);
                $trx->user()->associate($user);
                $trx->tabungan()->associate($tabungan);
                $trx->save();

                $transaksis[] = $trx;
            }

            \Illuminate\Support\Facades\DB::commit();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withErrors(['Gagal memproses data! Kesalahan sistem: ' . $e->getMessage()]);
        }

        // 3. PENTING: Eksekusi WA Blast di luar DB Transaction biar aplikasi gak nge-hang/timeout
        if (class_exists('\App\Jobs\SendWhatsAppMessage')) {
            foreach ($transaksis as $trx) {
                // Pastikan relasi user ter-load untuk kebutuhan template pesan WA
                $trx->load('user'); 
                \App\Jobs\SendWhatsAppMessage::dispatch($trx);
            }
        }

        return redirect()->back()
            ->with('success', true)
            ->with('alert-type', 'success')
            ->with('alert-message', count($transaksis) . ' data stor tabungan massal berhasil disimpan!')
            ->with('alert-duration', 4000);
    }

    /**
    * Menyimpan tarik tabungan siswa oleh Bendahara.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_tarikTabungan(Request $request)
    {
        // Bersihkan titik format ribuan
        $request->merge([
            'jumlah_tarik' => str_replace('.', '', $request->jumlah_tarik),
        ]);

        $validated = $request->validate([
            'kode'         => 'required',
            'name'         => 'required|not_in:Tidak Ada',
            'rombel'       => 'required',
            'jumlah_tarik' => 'required|numeric|min:1000',
        ], [
            'kode.required'         => 'Kode Siswa harus diisi.',
            'name.required'         => 'Nama harus diisi.',
            'name.not_in'           => 'Kode Siswa tidak ditemukan.',
            'rombel.required'       => 'Rombel harus diisi.',
            'jumlah_tarik.required' => 'Jumlah tarik harus diisi.',
            'jumlah_tarik.numeric'  => 'Jumlah tarik harus berupa angka.',
            'jumlah_tarik.min'      => 'Minimal tarik adalah Rp 1.000.',
        ]);

        $periodeId = \App\Helpers\PeriodeHelper::id();
        $user = \App\Models\User::findByKode($validated['kode'], $periodeId);

        if (!$user) {
            return back()->withErrors(['kode' => 'Kode siswa tidak ditemukan di periode aktif.']);
        }

        // Cari tabungan siswa
        $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
                        ->where('periode_id', $periodeId)
                        ->first();

        if (!$tabungan) {
            return back()->withErrors(['jumlah_tarik' => 'Siswa belum memiliki data tabungan di periode ini.']);
        }

        $awal  = $tabungan->saldo;
        $tarik = $validated['jumlah_tarik'];

        // Backend Protection: Hitung ulang premi 2.5% dan genapkan ke 1000 ke atas (biar gak bisa diakali dari inspect element)
        $premi = ceil(($tarik * 0.025) / 1000) * 1000;
        $totalPenarikan = $tarik + $premi;

        // Validasi Saldo Cukup atau Tidak
        if ($totalPenarikan > $awal) {
            return redirect()->back()
                ->withErrors(['jumlah_tarik' => 'Gagal! Total penarikan + admin (Rp ' . number_format($totalPenarikan, 0, ',', '.') . ') melebihi saldo tabungan.'])
                ->withInput();
        }

        $akhir = $awal - $totalPenarikan;

        // Simpan Transaksi Tarik
        $transaksi = new \App\Models\Transaksi([
            'jumlah_transaksi' => $totalPenarikan, // Dicatat total agar klop dengan saldo
            'saldo_awal'       => $awal,
            'saldo_akhir'      => $akhir,
            'tipe_transaksi'   => 'Tarik',
            'pembayaran'       => 'Tunai',
            'status'           => 'success',
            'pembuat'          => auth()->user()->name,
            'token_stor'       => \Illuminate\Support\Str::random(10),
            'periode_id'       => $periodeId,
        ]);
        $transaksi->user()->associate($user);
        $transaksi->tabungan()->associate($tabungan);
        $transaksi->save();

        // Update Tabungan Siswa
        $tabungan->update([
            'saldo' => $akhir,
            'premi' => $premi,
            'sisa'  => $akhir,
        ]);

        // Masukkan premi ke akun Bendahara (ID 2). Menggunakan firstOrCreate untuk safety
        $bendaharaTabungan = \App\Models\Tabungan::firstOrCreate(
            ['user_id' => 2, 'periode_id' => $periodeId],
            ['saldo' => 0, 'premi' => 0, 'sisa' => 0]
        );
        $bendaharaTabungan->increment('saldo', $premi);

        // // Jika Job Notifikasi ada
        // if(class_exists('\App\Jobs\SendWhatsAppMessage')) {
        //     \App\Jobs\SendWhatsAppMessage::dispatch($transaksi);
        // }

        return redirect()->back()
            ->with('success', 'Data tarik tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Penarikan atas nama '.$user->name.' berhasil diproses.')
            ->with('alert-duration', 3000);
    }

    // Walikelas ------------------------------------------------------------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Tabungan oleh Walikelas.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_index()
    {
        $walikelas = auth()->user();
        $periodeId = \App\Helpers\PeriodeHelper::id();
        $perPage = request('perPage', 10);

        // 1. Cari Rombel yang dipegang wali kelas ini pada periode aktif
        $rombel = \App\Models\Rombel::where('walikelas_id', $walikelas->id)
            ->where('periode_id', $periodeId)
            ->first();

        // Jika belum ada rombel, set 0 agar tidak error
        $rombelId = $rombel ? $rombel->id : 0;

        // 2. Buat Base Query (Pencarian Utama HARI INI untuk Rombel tersebut)
        // Pakai Clone biar gak perlu ngetik query yang sama berulang-ulang
        $baseQuery = \App\Models\Transaksi::where('periode_id', $periodeId)
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->where('status', 'success')
            ->whereHas('user.rombel', function ($query) use ($rombelId) {
                $query->where('rombels.id', $rombelId);
            });

        // 3. Eksekusi perhitungan menggunakan clone dari base query
        $transaksi_masuk = (clone $baseQuery)->where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
        $transaksi_keluar = (clone $baseQuery)->where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');

        $jumlah_saldo_tunai = (clone $baseQuery)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') 
                            - (clone $baseQuery)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');

        $jumlah_saldo_digital = (clone $baseQuery)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') 
                              - (clone $baseQuery)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        $storKali = (clone $baseQuery)->where('tipe_transaksi', 'Stor')->count();
        $tarikKali = (clone $baseQuery)->where('tipe_transaksi', 'Tarik')->count();

        // 4. Ambil data paginasi transaksi hari ini
        $transaksis = (clone $baseQuery)->latest()->paginate($perPage);

        return view('walikelas.tabungan.index', compact(
            'rombel', 'transaksi_masuk', 'transaksi_keluar', 
            'jumlah_saldo_tunai', 'jumlah_saldo_digital', 
            'transaksis', 'storKali', 'tarikKali'
        ));
    }

    /**
    * Menampilkan Dashboard Stor Tabungan oleh Walikelas.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_stor()
    {
        return view('walikelas.tabungan.stor');
    }

    /**
    * Menampilkan Dashboard Stor Tabungan Masal / Perkelas oleh Walikelas.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_storMasal(Request $request, $id = null)
    {
        $periodeId = PeriodeHelper::id();

        $rombelQuery = Rombel::with('walikelas')
            ->where('walikelas_id', auth()->id())
            ->where('periode_id', $periodeId);

        if ($id) {
            $rombelQuery->where('id', $id);
        }

        $rombel = $rombelQuery->first();

        if (!$rombel) {
            abort(403, 'Anda tidak memiliki rombel yang bisa diakses pada periode aktif ini.');
        }

        $siswa = User::where('roles_id', 4)
            ->whereHas('rombel', function ($q) use ($rombel) {
                $q->where('rombels.id', $rombel->id);
            })
            ->with([
                'rombel' => function ($q) use ($rombel) {
                    $q->where('rombels.id', $rombel->id);
                },
                'tabungan' => function ($q) use ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            ])
            ->get()
            ->sortBy(function ($user) {
                return $user->rombel->first()->pivot->kode ?? $user->name;
            });

        $kemarin = Carbon::yesterday()->toDateString();
        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->where('periode_id', $periodeId) // fix: konsisten filter periode juga di sini
            ->where('tipe_transaksi', 'Stor')
            ->whereHas('user.rombel', function ($q) use ($rombel) {
                $q->where('rombels.id', $rombel->id);
            })
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        return view('walikelas.tabungan.stor_masal', compact(
            'rombel', 'siswa', 'jumlahTransaksiKemarin'
        ));
    }

    /**
    * Mencari data tabungan siswa berdasarkan username yang diberikan.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\JsonResponse
    */

    public function walikelas_search(Request $request)
    {
        $kode = strtoupper(trim($request->get('kode')));
        $periodeId = PeriodeHelper::id();

        // ambil rombel yang dipegang walikelas ini di periode aktif
        // (biasanya 1, tapi whereIn jaga-jaga kalau suatu saat 1 walikelas pegang >1 rombel)
        $rombelIds = Rombel::where('walikelas_id', auth()->id())
            ->where('periode_id', $periodeId)
            ->pluck('id');

        if ($rombelIds->isEmpty()) {
            return response()->json([
                'name'     => 'Tidak Ada',
                'rombel'   => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
                'riwayat'  => [],
            ]);
        }

        // fix inti: cari siswa HANYA di antara rombel milik walikelas ini
        $user = User::whereHas('rombel', function ($q) use ($kode, $rombelIds, $periodeId) {
            $q->whereIn('rombels.id', $rombelIds)
            ->where('rombels.periode_id', $periodeId)
            ->where('rombel_user.kode', $kode);
        })->first();

        if (!$user) {
            return response()->json([
                'name'     => 'Tidak Ada',
                'rombel'   => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
                'riwayat'  => [],
            ]);
        }

        $rombelSekarang = $user->rombel()
            ->whereIn('rombels.id', $rombelIds)
            ->where('rombels.periode_id', $periodeId)
            ->first();

        // fix: query eksplisit, jangan andalkan relasi tunggal yang ambigu di lintas periode
        $tabungan = Tabungan::where('user_id', $user->id)
            ->where('periode_id', $periodeId)
            ->first();

        $saldo = $tabungan->saldo ?? 0;

        $riwayat = Transaksi::where('user_id', $user->id)
            ->where('periode_id', $periodeId)
            ->where('status', 'success')
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get()
            ->map(function ($t) {
                return [
                    'tanggal'     => $t->created_at->format('d M, H:i'),
                    'tipe'        => strtolower($t->tipe_transaksi),
                    'jumlah'      => number_format($t->jumlah_transaksi, 0, ',', '.'),
                    'saldo_akhir' => number_format($t->saldo_akhir, 0, ',', '.'),
                    'pembayaran'  => $t->pembayaran,
                ];
            });

        return response()->json([
            'name'     => $user->name,
            'rombel'   => $rombelSekarang->name ?? 'Tidak Ada',
            'tabungan' => number_format($saldo, 0, ',', '.'),
            'riwayat'  => $riwayat,
        ]);
    }


    /**
    * Menyimpan stor tabungan siswa oleh Walikelas.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_storTabungan(Request $request)
    {
        $request->merge([
            'jumlah_stor' => preg_replace('/\D+/', '', $request->jumlah_stor),
        ]);

        $validated = $request->validate([
            'kode'         => 'required',
            'name'         => 'required|not_in:Tidak Ada',
            'rombel'       => 'required',
            'jumlah_stor'  => 'required|numeric|min:1000',
        ], [
            'kode.required'        => 'Kode Siswa harus diisi.',
            'name.required'        => 'Nama harus diisi.',
            'name.not_in'          => 'Kode Siswa tidak ditemukan.',
            'rombel.required'      => 'Rombel harus diisi.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric'  => 'Jumlah stor harus berupa angka.',
            'jumlah_stor.min'      => 'Minimal stor adalah Rp 1.000.',
        ]);

        $periodeId = PeriodeHelper::id();
        $kode = strtoupper(trim($validated['kode']));

        // fix: re-validasi di server, JANGAN percaya field hidden dari form begitu saja --
        // pastikan kode ini beneran ada di salah satu rombel milik walikelas yang login
        $rombelIds = Rombel::where('walikelas_id', auth()->id())
            ->where('periode_id', $periodeId)
            ->pluck('id');

        $user = User::whereHas('rombel', function ($q) use ($kode, $rombelIds, $periodeId) {
            $q->whereIn('rombels.id', $rombelIds)
            ->where('rombels.periode_id', $periodeId)
            ->where('rombel_user.kode', $kode);
        })->first();

        if (!$user) {
            return back()->withErrors(['kode' => 'Kode siswa tidak ditemukan di rombel yang Anda pegang.']);
        }

        $tabungan = Tabungan::firstOrCreate(
            ['user_id' => $user->id, 'periode_id' => $periodeId],
            ['saldo' => 0, 'premi' => 0, 'sisa' => 0]
        );

        $awal  = $tabungan->saldo;
        $stor  = $validated['jumlah_stor'];
        $akhir = $awal + $stor;

        $premi = $akhir * 0.025;
        $sisa  = $akhir - $premi;

        $transaksi = new Transaksi([
            'jumlah_transaksi' => $stor,
            'saldo_awal'       => $awal,
            'saldo_akhir'      => $akhir,
            'tipe_transaksi'   => 'Stor',
            'pembayaran'       => 'Tunai',
            'status'           => 'success',
            'pembuat'          => auth()->user()->name,
            'token_stor'       => Str::random(10),
            'periode_id'       => $periodeId, // fix: sebelumnya belum diisi
        ]);
        $transaksi->user()->associate($user);
        $transaksi->tabungan()->associate($tabungan);
        $transaksi->save();
        $transaksi->load('user');

        $tabungan->update([
            'saldo' => $akhir,
            'premi' => $premi,
            'sisa'  => $sisa,
        ]);

        if (class_exists('\App\Jobs\SendWhatsAppMessage')) {
            SendWhatsAppMessage::dispatch($transaksi);
        }

        return redirect()->back()
            ->with('success', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data stor tabungan atas nama ' . $user->name . ' berhasil disimpan.')
            ->with('alert-duration', 3000);
    }

    /**
    * Menyimpan stor tabungan siswa secara masal / perkelas oleh Walikelas.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_storMasalTabungan(Request $request)
    {
        $periodeId = PeriodeHelper::id();
        $rombelId  = $request->input('rombel_id');

        // fix inti: pastikan rombel yang dikirim form ini beneran milik walikelas yang login
        $rombel = Rombel::where('id', $rombelId)
            ->where('walikelas_id', auth()->id())
            ->where('periode_id', $periodeId)
            ->first();

        if (!$rombel) {
            return redirect()->back()->withErrors(['Rombel tidak valid atau bukan milik Anda.']);
        }

        $inputs = $request->input('input', []);

        $sanitized = [];
        foreach ($inputs as $row) {
            $stor = preg_replace('/\D+/', '', $row['stor'] ?? '');

            if (!empty($row['kode']) && is_numeric($stor) && $stor >= 1000) {
                $sanitized[] = [
                    'kode' => strtoupper(trim($row['kode'])),
                    'stor' => (int) $stor,
                ];
            }
        }

        if (empty($sanitized)) {
            return redirect()->back()->withErrors(['Data kosong atau tidak valid. Pastikan minimal stor adalah Rp 1.000.']);
        }

        // fix inti: whitelist kode-kode yang memang anggota SAH rombel ini,
        // biar gak bisa nyelundup kode siswa dari rombel/kelas lain
        $kodeValidDiRombelIni = DB::table('rombel_user')
            ->where('rombel_id', $rombel->id)
            ->pluck('kode')
            ->map(fn ($k) => strtoupper($k))
            ->toArray();

        $sanitized = array_filter($sanitized, function ($row) use ($kodeValidDiRombelIni) {
            return in_array($row['kode'], $kodeValidDiRombelIni);
        });

        if (empty($sanitized)) {
            return redirect()->back()->withErrors(['Tidak ada data valid milik rombel Anda yang bisa diproses.']);
        }

        $transaksis = [];

        DB::beginTransaction();
        try {
            foreach ($sanitized as $data) {
                $user = User::findByKode($data['kode'], $periodeId);
                if (!$user) continue;

                $tabungan = Tabungan::firstOrCreate(
                    ['user_id' => $user->id, 'periode_id' => $periodeId],
                    ['saldo' => 0, 'premi' => 0, 'sisa' => 0]
                );

                $tabungan = Tabungan::where('id', $tabungan->id)->lockForUpdate()->first();

                $stor  = $data['stor'];
                $awal  = $tabungan->saldo;
                $akhir = $awal + $stor;

                $premiSiswa = $akhir * 0.025;
                $sisa       = $akhir - $premiSiswa;

                $tabungan->update([
                    'saldo' => $akhir,
                    'premi' => $premiSiswa,
                    'sisa'  => $sisa,
                ]);

                $trx = new Transaksi([
                    'jumlah_transaksi' => $stor,
                    'saldo_awal'       => $awal,
                    'saldo_akhir'      => $akhir,
                    'tipe_transaksi'   => 'Stor',
                    'pembayaran'       => 'Tunai',
                    'status'           => 'success',
                    'pembuat'          => auth()->user()->name,
                    'token_stor'       => Str::random(10),
                    'periode_id'       => $periodeId,
                ]);
                $trx->user()->associate($user);
                $trx->tabungan()->associate($tabungan);
                $trx->save();

                $transaksis[] = $trx;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Gagal memproses data! Kesalahan sistem: ' . $e->getMessage()]);
        }

        if (class_exists('\App\Jobs\SendWhatsAppMessage')) {
            foreach ($transaksis as $trx) {
                $trx->load('user');
                \App\Jobs\SendWhatsAppMessage::dispatch($trx);
            }
        }

        return redirect()->back()
            ->with('success', true)
            ->with('alert-type', 'success')
            ->with('alert-message', count($transaksis) . ' data stor tabungan massal berhasil disimpan!')
            ->with('alert-duration', 4000);
    }

    // Siswa ------------------------------------------------------------------------------------------------------------------------------------------------
    
    /**
    * Menampilkan Dashboard Tarik Tabungan oleh Siswa.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_tarik()
    {
        $user = auth()->user();
        $periodeAktif = \App\Helpers\PeriodeHelper::id();

        $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
            ->where('periode_id', $periodeAktif)
            ->first();

        $nominal = $tabungan ? $tabungan->saldo : 0;
        $terbilang = \App\Helpers\RupiahHelper::terbilangRupiah($nominal);
        $rombel = $user->rombel()->where('rombels.periode_id', $periodeAktif)->first();

        // Ambil pengajuan terakhir untuk mengecek status (Pending, Processing, Succeeded, Tolak)
        $pengajuan = \App\Models\Pengajuan::where('user_id', $user->id)
            ->where('periode_id', $periodeAktif)
            ->where('status', '!=', 'Batal')
            ->latest()
            ->first();

        return view('siswa.tabungan.tarik', compact('nominal', 'terbilang', 'pengajuan', 'rombel'));
    }
    
    
    /**
    * Menampilkan Dashboard Stor Tabungan oleh Siswa.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_stor()
    {
        $user = auth()->user();
        $periodeAktif = \App\Helpers\PeriodeHelper::id();

        $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
            ->where('periode_id', $periodeAktif)
            ->first();

        $nominal = $tabungan ? $tabungan->saldo : 0;
        $terbilang = \App\Helpers\RupiahHelper::terbilangRupiah($nominal);
        $rombel = $user->rombel()->where('rombels.periode_id', $periodeAktif)->first();

        $invoice = null;
        
        // Ambil transaksi aktif (Pending atau Processing)
        $transaksiAktif = \App\Models\Transaksi::where('user_id', $user->id)
            ->where('periode_id', $periodeAktif)
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->first();

        if ($transaksiAktif && $transaksiAktif->external_id) {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
                ->get('https://api.xendit.co/v2/invoices', [
                    'external_id' => $transaksiAktif->external_id,
                ]);

            if ($response->successful()) {
                $invoices = $response->json();
                if (is_array($invoices) && count($invoices) > 0) {
                    $xenditInvoice = $invoices[0];
                    $xenditStatus = $xenditInvoice['status'] ?? 'PENDING';

                    if ($xenditStatus === 'EXPIRED') {
                        $transaksiAktif->status = 'Expired';
                        $transaksiAktif->save();
                        $transaksiAktif = null; 
                    } elseif ($xenditStatus === 'PAID' || $xenditStatus === 'SETTLED') {
                        if ($transaksiAktif->status !== 'success') {
                            \Illuminate\Support\Facades\DB::transaction(function() use ($transaksiAktif, $tabungan) {
                                $transaksiAktif->status = 'success';
                                $transaksiAktif->save();
                                if ($tabungan) {
                                    $tabungan->saldo += $transaksiAktif->jumlah_transaksi;
                                    $tabungan->save();
                                }
                            });
                            $nominal = $tabungan ? $tabungan->refresh()->saldo : 0;
                            $terbilang = \App\Helpers\RupiahHelper::terbilangRupiah($nominal);
                        }
                        $transaksiAktif = null; 
                    } else {
                        $invoice = $xenditInvoice;
                        $invoice['expiry_date_carbon'] = \Carbon\Carbon::parse($invoice['expiry_date']);
                    }
                }
            }
        }

        return view('siswa.tabungan.stor', compact('nominal', 'terbilang', 'invoice', 'rombel', 'transaksiAktif'));
    }

    /**
     * Menangani Pembatalan Transaksi/Invoice Aktif oleh Siswa
     */
    public function batal($id)
    {
        $periodeAktif = \App\Helpers\PeriodeHelper::id();

        // Ambil data transaksi pending siswa yang akan dibatalkan
        $transaksi = \App\Models\Transaksi::where('user_id', $id)
            ->where('periode_id', $periodeAktif)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi aktif tidak ditemukan.');
        }

        // Hit API Xendit untuk mematikan paksa (expire) invoice agar tautan lamanya mati demi keamanan
        if ($transaksi->external_id) {
            $responseFetch = \Illuminate\Support\Facades\Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
                ->get('https://api.xendit.co/v2/invoices', [
                    'external_id' => $transaksi->external_id,
                ]);

            if ($responseFetch->successful()) {
                $invoices = $responseFetch->json();
                if (is_array($invoices) && count($invoices) > 0) {
                    $invoiceIdXendit = $invoices[0]['id'];
                    
                    // Trigger expire token ke server Xendit
                    \Illuminate\Support\Facades\Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
                        ->post("https://api.xendit.co/v2/invoices/{$invoiceIdXendit}/expire!");
                }
            }
        }

        // Ubah status lokal di database aplikasi menjadi Batal
        $transaksi->status = 'Batal';
        $transaksi->save();

        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Transaksi sebelumnya berhasil dibatalkan. Silakan isi form kembali.')
            ->with('alert-duration', 5000);
    }

    public function createInvoice(Request $request)
    {
        $request->validate([
            'jumlah_stor' => ['required', 'numeric', 'min:10000'],
        ]);

        $user = auth()->user();
        $periodeAktif = \App\Helpers\PeriodeHelper::id();

        $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
            ->where('periode_id', $periodeAktif)
            ->first();

        if (!$tabungan) {
            return back()->with('error', 'Data tabungan Anda tidak ditemukan.');
        }

        $jumlahStor = $request->jumlah_stor;

        // =================================================================
        // WAJIB SET API KEY GLOBAL SEBELUM INSTANSIASI SDK XENDIT BARU
        // =================================================================
        \Xendit\Configuration::setXenditKey(config('services.xendit.secret_key'));
        
        $apiInstance = new \Xendit\Invoice\InvoiceApi();
        // =================================================================

        $externalId = 'stor-' . $user->id . '-' . time();
        $successRedirectUrl = route('siswa.dashboard');

        $invoiceRequest = new \Xendit\Invoice\CreateInvoiceRequest([
            'external_id' => $externalId,
            'payer_email' => $user->email,
            'description' => 'Stor Tabungan untuk ' . $user->name,
            'amount' => (int) $jumlahStor,
            'invoice_duration' => 86400,
            'success_redirect_url' => $successRedirectUrl,
        ]);

        try {
            $invoice = $apiInstance->createInvoice($invoiceRequest);

            $transaksi = new \App\Models\Transaksi();
            $transaksi->jumlah_transaksi = $jumlahStor;
            $transaksi->saldo_awal = $tabungan->saldo;
            $transaksi->saldo_akhir = $tabungan->saldo + $jumlahStor;
            $transaksi->tipe_transaksi = 'Stor';
            $transaksi->pembayaran = 'Digital';
            $transaksi->status = 'pending';
            $transaksi->pembuat = $user->name;
            $transaksi->checkout_link = $invoice['invoice_url'];
            $transaksi->external_id = $externalId;
            $transaksi->token_stor = \Illuminate\Support\Str::random(10);
            $transaksi->user_id = $user->id;
            $transaksi->tabungan_id = $tabungan->id;
            // FIX FATAL: Wajib masukkin periode_id biar kecatat di laporan
            $transaksi->periode_id = $periodeAktif; 
            $transaksi->save();

            return redirect($invoice['invoice_url']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Menangani webhook dari Xendit untuk payment yang berhasil.
     */
    public function handleWebhook(Request $request)
    {
        // Proteksi: Ambil token verification dari header yang dikirim Xendit
        $xenditXCallbackToken = $request->header('x-callback-token');

        // Cocokan dengan token yang kita simpan di config/services.php
        if ($xenditXCallbackToken !== config('services.xendit.webhook_token')) {
            return response()->json(['message' => 'Unauthorized Webhook Token'], 401);
        }

        $data = $request->all();

        if (!isset($data['external_id']) || !isset($data['status'])) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $transaksi = \App\Models\Transaksi::where('external_id', $data['external_id'])->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaksi->status === 'success') {
            return response()->json(['message' => 'Payment telah diproses']);
        }

        if ($data['status'] === 'PAID') {
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                $transaksi->status = 'success';
                $transaksi->save();

                // Update saldo Tabungan (Aman karena tabungan_id sudah diset saat pembuatan invoice)
                $tabungan = $transaksi->tabungan;
                if ($tabungan) {
                    $tabungan->saldo += $transaksi->jumlah_transaksi;
                    $tabungan->save();
                }

                $user = \App\Models\User::find($transaksi->user_id);
                
                // Trigger job WA
                \App\Jobs\SendWhatsAppMessage::dispatch($transaksi);

                \Illuminate\Support\Facades\DB::commit();
                return response()->json(['message' => 'Payment success, whatsapp attempted']);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                \Illuminate\Support\Facades\Log::error('Handle Webhook Error: ' . $e->getMessage());
                return response()->json(['message' => 'Server Error'], 500);
            }
        }

        return response()->json(['message' => 'Success']);
    }
}
