<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Xendit\Xendit;
use Xendit\Invoice;
use App\Models\User;
use Xendit\Invoices;
use App\Models\Kelas;
use App\Models\Rombel;
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
        
        // Ambil info periode untuk ditampilkan di UI (Asumsi lu punya relasi/helper untuk ini)
        // Jika tidak ada nama periode di helper, lu bisa sesuaikan.
        $periodeAktif = \App\Models\Periode::find($periodeId); 

        // 1. Hitung Total Global (Hanya untuk transaksi HARI INI dan SUCCESS)
        $baseGlobalQuery = Transaksi::whereDate('created_at', Carbon::today())
                                    ->where('status', 'success');

        $transaksi_masuk  = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
        $transaksi_keluar = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');
        
        $storKali  = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->count();
        $tarikKali = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->count();

        $jumlah_saldo_tunai = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi') 
                            - (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Tunai')->sum('jumlah_transaksi');

        $jumlah_saldo_digital = (clone $baseGlobalQuery)->where('tipe_transaksi', 'Stor')->where('pembayaran', 'Digital')->sum('jumlah_transaksi') 
                              - (clone $baseGlobalQuery)->where('tipe_transaksi', 'Tarik')->where('pembayaran', 'Digital')->sum('jumlah_transaksi');

        // 2. Ambil Semua Rombel Aktif di Periode Ini
        $rombels = Rombel::where('periode_id', $periodeId)->orderBy('name')->get();
        $rombelList = [];

        // 3. Tarik Data Transaksi Hari Ini Per Rombel
        foreach ($rombels as $rombel) {
            $baseRombelQuery = Transaksi::whereHas('user.rombel', function($q) use ($rombel) {
                $q->where('rombels.id', $rombel->id);
            })
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'success');

            $transaksi_hari_ini = (clone $baseRombelQuery)->orderBy('created_at', 'desc')->get();
            $stor_rombel  = (clone $baseRombelQuery)->where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
            $tarik_rombel = (clone $baseRombelQuery)->where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');

            $rombelList[$rombel->id] = [
                'nama_rombel' => $rombel->name,
                'data'  => $transaksi_hari_ini,
                'stor'  => $stor_rombel,
                'tarik' => $tarik_rombel,
            ];
        }

        return view('bendahara.tabungan.index', compact(
            'periodeAktif',
            'transaksi_masuk', 
            'transaksi_keluar', 
            'jumlah_saldo_tunai', 
            'jumlah_saldo_digital', 
            'rombelList', 
            'rombels',
            'storKali', 
            'tarikKali'
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
        $periodeId = \App\Helpers\PeriodeHelper::id();
        
        // 1. Ambil data Rombel sekalian bawa relasi walikelasnya
        $rombel = \App\Models\Rombel::with('walikelas')->findOrFail($id);

        // 2. Ambil data siswa
        $siswa = \App\Models\User::where('roles_id', 4)
            ->whereHas('rombel', function($q) use ($id) {
                $q->where('rombels.id', $id);
            })
            ->with([
                'rombel' => function($q) use ($id) {
                    $q->where('rombels.id', $id);
                },
                'tabungan' => function($q) use ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            ])
            ->get()
            // Urutkan berdasarkan kode absen (1A01, 1A02)
            ->sortBy(function($user) {
                return $user->rombel->first()->pivot->kode ?? $user->name;
            });

        // 3. Hitung transaksi khusus "Stor" di H-1 untuk Rombel ini
        $kemarin = \Carbon\Carbon::yesterday()->toDateString();
        $jumlahTransaksiKemarin = \App\Models\Transaksi::whereDate('created_at', $kemarin)
            ->where('tipe_transaksi', 'Stor')
            ->whereHas('user.rombel', function($q) use ($id) {
                $q->where('rombels.id', $id);
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

        // Cari user menggunakan fungsi findByKode yang ada di Model User
        $user = User::findByKode($kode, $periodeId);

        if ($user) {
            // Load relasi tabungan dan rombel khusus periode ini
            $user->load(['tabungan', 'rombel' => function($q) use ($periodeId) {
                $q->where('rombels.periode_id', $periodeId);
            }]);

            $namaRombel = $user->rombel->first()->name ?? 'Tidak Ada';
            $saldo = $user->tabungan ? $user->tabungan->saldo : 0;

            return response()->json([
                'name'     => $user->name,
                'rombel'   => $namaRombel,
                'tabungan' => number_format($saldo, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'name'     => 'Tidak Ada',
            'rombel'   => 'Tidak Ada',
            'tabungan' => 'Tidak Ada',
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
        $kelas_id = Auth::user()->kelas_id;
        $perPage = request('perPage', 10);

        $transaksi_masuk = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Stor')
        ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $jumlah_saldo_tunai = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Stor')
        ->where('status', 'success')
        ->where('pembayaran', 'Tunai')->where('periode_id',PeriodeHelper::id())
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi')
        - Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->where('status', 'success')
        ->where('pembayaran', 'Tunai')->where('periode_id',PeriodeHelper::id())
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $jumlah_saldo_digital = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Stor')
        ->where('status', 'success')
        ->where('pembayaran', 'Digital')->where('periode_id',PeriodeHelper::id())
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi')
        - Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->where('status', 'success')
        ->where('pembayaran', 'Digital')->where('periode_id',PeriodeHelper::id())
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $kelas = Transaksi::whereHas('user.kelas', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->whereDate('created_at', Carbon::today())
        ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
        ->paginate($perPage);

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->where('created_at', '>=', Carbon::today()->startOfDay())
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id);
            })
            ->count();

        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')->where('periode_id',PeriodeHelper::id())
            ->where('created_at', '>=', Carbon::today()->startOfDay())
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id);
            })
            ->count();

        return view('walikelas.tabungan.index', compact('transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo_tunai', 'jumlah_saldo_digital', 'kelas', 'storKali', 'tarikKali'));
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

    public function walikelas_storMasal()
    {
        $kelas = auth()->user()->kelas;
        dd($kelas);
        $siswa = Tabungan::where('periode_id', PeriodeHelper::id())
            ->whereHas('user', function ($query) use ($kelas) {
                $query->where('kelas_id', $kelas->id)
                    ->where('roles_id', 4);
            })
            ->with('user')
            ->get();
        $walikelas = User::where('kelas_id', $kelas->id)->where('roles_id', 3)->first();
        $kemarin = Carbon::yesterday()->toDateString();

        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->whereHas('user', function ($query) use ($kelas) {
                $query->where('kelas_id', $kelas->id);
            })
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        return view('walikelas.tabungan.stor_masal', compact('kelas', 'siswa', 'walikelas', 'jumlahTransaksiKemarin'));
    }

    /**
    * Mencari data tabungan siswa berdasarkan username yang diberikan.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\JsonResponse
    */

    public function walikelas_search(Request $request)
    {
        $username = $request->get('username');
        $kelasIdWali = auth()->user()->kelas_id; // ambil kelas_id dari walikelas yang login

        $user = DB::table('users')
            ->join('tabungans', 'tabungans.user_id', '=', 'users.id')
            ->join('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->where('users.username', $username)
            ->where('users.kelas_id', $kelasIdWali) // batasi hanya siswa di kelas walikelas
            ->select(
                'users.name as user_name',
                'kelas.name as kelas_name',
                'tabungans.saldo as tabungan_saldo'
            )
            ->first();

        if ($user) {
            return response()->json([
                'name' => $user->user_name,
                'kelas' => $user->kelas_name,
                'tabungan' => number_format($user->tabungan_saldo, 0, ',', '.'),
            ]);
        } else {
            return response()->json([
                'name' => 'Tidak Ada',
                'kelas' => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
            ]);
        }
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
            'username'     => 'required|exists:users,username',
            'name'         => 'required',
            'kelas'        => 'required',
            'jumlah_stor'  => 'required|numeric|min:1000',
        ], [
            'username.required'    => 'Id Tabungan harus diisi.',
            'username.exists'      => 'Id Tabungan tidak ditemukan.',
            'name.required'        => 'Nama harus diisi.',
            'kelas.required'       => 'Kelas harus diisi.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric'  => 'Jumlah stor harus berupa angka.',
            'jumlah_stor.min'      => 'Minimal stor adalah 1.000.',
        ]);

        $user     = User::where('username', $validated['username'])->firstOrFail();
        $tabungan = $user->tabungan;

        $awal  = $tabungan->saldo;
        $stor  = $validated['jumlah_stor'];
        $akhir = $awal + $stor;

        // Hitung premi & sisa
        $premi = $akhir * 0.025;
        $sisa  = $akhir - $premi;

        // Simpan transaksi
        $transaksi = new Transaksi([
            'jumlah_transaksi' => $stor,
            'saldo_awal'       => $awal,
            'saldo_akhir'      => $akhir,
            'tipe_transaksi'   => 'Stor',
            'pembayaran'       => 'Tunai',
            'status'           => 'success',
            'pembuat'          => auth()->user()->name,
            'token_stor'       => Str::random(10),
        ]);

        $transaksi->user()->associate($user);
        $transaksi->tabungan()->associate($tabungan);
        $transaksi->save();
        $transaksi->load('user'); // Penting untuk WA job

        // Update saldo tabungan
        $tabungan->update([
            'saldo' => $akhir,
            'premi' => $premi,
            'sisa'  => $sisa,
        ]);

        // Kirim WA
        SendWhatsAppMessage::dispatch($transaksi);

        return redirect()->back()
            ->with('success', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data stor tabungan berhasil disimpan.')
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
        $sanitized = collect($request->input('input', []))
            ->map(fn($row) => [
                'username' => $row['username'] ?? null,
                'stor'     => preg_replace('/\D+/', '', $row['stor'] ?? ''),
            ])
            ->filter(fn($row) => $row['username'] && is_numeric($row['stor']) && (int)$row['stor'] >= 1000)
            ->values()
            ->toArray();

        $request->merge(['input' => $sanitized]);

        $request->validate([
            'input.*.username' => 'required|string|exists:users,username',
            'input.*.stor'     => 'required|numeric|min:1000',
        ], [
            'input.*.stor.min' => 'Minimal stor Rp 1.000',
        ]);

        foreach ($request->input('input') as $row) {
            $user = User::where('username', $row['username'])->first();
            if (!$user || !$user->tabungan) continue;

            $tabungan = $user->tabungan;
            $awal     = $tabungan->saldo;
            $stor     = (int)$row['stor'];
            $akhir    = $awal + $stor;

            $premi = $akhir * 0.025;
            $sisa  = $akhir - $premi;

            $tabungan->update([
                'saldo' => $akhir,
                'premi' => $premi,
                'sisa'  => $sisa,
            ]);

            $transaksi = Transaksi::create([
                'user_id'          => $user->id,
                'tabungan_id'      => $tabungan->id,
                'jumlah_transaksi' => $stor,
                'saldo_awal'       => $awal,
                'saldo_akhir'      => $akhir,
                'tipe_transaksi'   => 'Stor',
                'pembayaran'       => 'Tunai',
                'status'           => 'success',
                'pembuat'          => auth()->user()->name,
                'token_stor'       => Str::random(10),
            ]);

            $transaksi->user()->associate($user);
            $transaksi->tabungan()->associate($tabungan);
            $transaksi->save();
            $transaksi->load('user');
            SendWhatsAppMessage::dispatch($transaksi);
        }
        return redirect()->back()
            ->with('success', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-duration', 3000);
    }

    // Siswa ------------------------------------------------------------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Stor Tabungan oleh Siswa.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_stor()
    {
        $user = auth()->user();

        $nominal = $user->tabungan ? $user->tabungan->saldo : 0;
        $terbilang = RupiahHelper::terbilangRupiah($nominal);

        $invoice = null;

        $pendingTransaksi = Transaksi::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingTransaksi && $pendingTransaksi->external_id) {
            $response = Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
                ->get('https://api.xendit.co/v2/invoices', [
                    'external_id' => $pendingTransaksi->external_id,
                ]);

            $invoices = $response->json();

            if ($response->successful() && is_array($invoices) && count($invoices) > 0) {
                $invoice = $invoices[0];
                $invoice['expiry_date_carbon'] = Carbon::parse($invoice['expiry_date']);
            }
        }

        return view('siswa.tabungan.stor', compact('nominal', 'terbilang', 'invoice'));
    }

    /**
    * Menampilkan Batal Stor Tabungan oleh Siswa.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function batal($id)
    {
        $transaksi = Transaksi::where('user_id', $id)->latest()->first();


        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        }

        $transaksi->status = 'Batal';
        $transaksi->save();

        return redirect()->back()->with('success', 'Transaksi Telah di Batalkan.')
        ->with('alert-type', 'success')
        ->with('alert-message', 'Transaksi Telah di Batalkan.')
        ->with('alert-duration', 30000);
    }

    /**
    * Menampilkan Dashboard Tarik Tabungan oleh Siswa.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function siswa_tarik()
    {
        $pengajuans = Pengajuan::where('status', 'pending')->where('user_id', Auth::id())->first();

        $nominal = auth()->user()->tabungan->saldo ;
        $terbilang = RupiahHelper::terbilangRupiah($nominal);
        return view('siswa.tabungan.tarik', compact('nominal', 'terbilang'));
    }

    /**
    * Menangani Stor Tabungan Siswa oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function createInvoice(Request $request)
    {
        $request->validate([
            'jumlah_stor' => ['required', 'numeric', 'min:10000'],
        ], [
            'jumlah_stor.min' => 'Jumlah stor minimal Rp10.000.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric' => 'Jumlah stor harus berupa angka.',
        ]);

        $user = Auth::user();

        if (!$user->tabungan) {
            return back()->with('error', 'Tabungan tidak ditemukan.');
        }

        $jumlahStor = $request->jumlah_stor;

        $apiInstance = new InvoiceApi();

        $externalId = 'stor-' . $user->id . '-' . time();
        $successRedirectUrl = route('siswa.dashboard');

        $invoiceRequest = new CreateInvoiceRequest([
            'external_id' => $externalId,
            'payer_email' => $user->email,
            'description' => 'Stor Tabungan untuk ' . $user->name,
            'amount' => (int) $jumlahStor,
            'invoice_duration' => 86400,
            'success_redirect_url' => $successRedirectUrl,
        ]);

        try {
            $invoice = $apiInstance->createInvoice($invoiceRequest);

            $transaksi = new Transaksi();
            $transaksi->jumlah_transaksi = $jumlahStor;
            $transaksi->saldo_awal = $user->tabungan->saldo;
            $transaksi->saldo_akhir = $user->tabungan->saldo + $jumlahStor;
            $transaksi->tipe_transaksi = 'Stor';
            $transaksi->pembayaran = 'Digital';
            $transaksi->status = 'pending';
            $transaksi->pembuat = $user->name;
            $transaksi->checkout_link = $invoice['invoice_url'];
            $transaksi->external_id = $externalId;
            $transaksi->token_stor = Str::random(10);
            $transaksi->user_id = $user->id;
            $transaksi->tabungan_id = $user->tabungan->id;
            $transaksi->save();

            return redirect($invoice['invoice_url']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    /**
    * Menangani webhook dari Xendit untuk payment yang berhasil.
    *
    * @param Request $request Data yang dikirimkan oleh Xendit melalui webhook.
    * @return \Illuminate\Http\JsonResponse Respon JSON dengan status sukses atau error.
    */

    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        if (!isset($data['external_id']) || !isset($data['status'])) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $transaksi = Transaksi::where('external_id', $data['external_id'])->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaksi->status === 'success') {
            return response()->json(['message' => 'Payment telah diproses']);
        }

        if ($data['status'] === 'PAID') {
            \DB::beginTransaction();
            try {
                $transaksi->status = 'success';
                $transaksi->save();

                $tabungan = $transaksi->tabungan;
                $tabungan->saldo += $transaksi->jumlah_transaksi;
                $tabungan->save();

                $user = User::find($transaksi->user_id);

                SendWhatsAppMessage::dispatch($transaksi);

                \DB::commit();
                return response()->json(['message' => 'Payment success, email/whatsapp attempted']);
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Handle Webhook Error: ' . $e->getMessage());
                return response()->json(['message' => 'Server Error'], 500);
            }
        }

        return response()->json(['message' => 'Success']);
    }
}
