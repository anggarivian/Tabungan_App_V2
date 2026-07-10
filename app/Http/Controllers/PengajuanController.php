<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use Xendit\Configuration;
use Illuminate\Support\Str;
use App\Mail\TabunganAjukan;
use Illuminate\Http\Request;
use Xendit\Payout\PayoutApi;
use App\Helpers\RupiahHelper;
use App\Mail\TabunganTarikMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Xendit\Payout\CreatePayoutRequest;

class PengajuanController extends Controller
{
    protected $payoutApi;

    /**
     * Konstruktor untuk menginisialisasi Payout API dan mengatur kunci API Xendit.
     *
     * @param PayoutApi $payoutApi Instance dari PayoutApi yang digunakan untuk transaksi payout.
     */
    public function __construct(PayoutApi $payoutApi)
    {
        $this->payoutApi = new PayoutApi();
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    // Bendahara Kelola Pengajuan --------------------------------------------------------------------------------
    /**
    * Menampilkan Data Pengajuan Penarikan Tabungan oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function kelola_pengajuan(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $periodeAktif = \App\Helpers\PeriodeHelper::id();

        // Ambil pengajuan berstatus pending khusus di periode aktif
        $query = \App\Models\Pengajuan::where('status', 'pending')
            ->where('periode_id', $periodeAktif)
            ->with(['user.rombel' => function($q) use ($periodeAktif) {
                $q->where('rombels.periode_id', $periodeAktif);
            }, 'tabungan']);

        $searchTerm = $request->input('search');
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $periodeAktif) {
                $q->where('jumlah_penarikan', 'LIKE', '%'.$searchTerm.'%')
                  ->orWhere('pembayaran', 'LIKE', '%'.$searchTerm.'%')
                  ->orWhere('alasan', 'LIKE', '%'.$searchTerm.'%')
                  
                  // Pencarian berdasarkan nama/username siswa
                  ->orWhereHas('user', function ($qu) use ($searchTerm) {
                      $qu->where('name', 'LIKE', '%'.$searchTerm.'%')
                         ->orWhere('username', 'LIKE', '%'.$searchTerm.'%');
                  })
                  
                  // Pencarian berdasarkan nama Rombel siswa di periode aktif
                  ->orWhereHas('user.rombel', function ($qr) use ($searchTerm, $periodeAktif) {
                      $qr->where('rombels.name', 'LIKE', '%'.$searchTerm.'%')
                         ->where('rombels.periode_id', $periodeAktif);
                  });
            });
        }

        $query->orderBy('created_at', 'desc');
        $pengajuan = $query->paginate($perPage)->withQueryString();

        return view('bendahara.kelola_pengajuan', compact('pengajuan'));
    }

    /**
     * Mengambil data pengajuan berdasarkan ID untuk Modal AJAX
     */
    public function getPengajuanData($id)
    {
        $pengajuan = \App\Models\Pengajuan::with(['tabungan', 'user'])->findOrFail($id);
        
        // Cari rombel siswa yang sesuai dengan periode pengajuan tersebut dibuat
        $rombel = $pengajuan->user->rombel()
            ->where('rombels.periode_id', $pengajuan->periode_id)
            ->first();

        return response()->json([
            'pengajuan_id'      => $pengajuan->id,
            'user_id'           => $pengajuan->user->id ?? '-',
            'username'          => $pengajuan->user->username ?? '-',
            'name'              => $pengajuan->user->name ?? '-',
            'kelas'             => $rombel->name ?? '-',
            'tabungan'          => $pengajuan->tabungan->saldo ?? 0, 
            'jumlah_penarikan'  => $pengajuan->jumlah_penarikan,
            'status'            => $pengajuan->status,
            'pembayaran'        => $pengajuan->pembayaran,
            'metode_digital'    => $pengajuan->metode_digital ?? '-',
            'type_tujuan'       => $pengajuan->bank_code ?? $pengajuan->ewallet_type ?? '-',
            'nomor_tujuan'      => $pengajuan->nomor_rekening ?? $pengajuan->ewallet_number ?? '-',
            'alasan'            => $pengajuan->alasan,
        ]);
    }

    // Siswa Ajukan Penarikan Tabungan ---------------------------------------------------------------------------
    /**
    * Menangani Pengajuan Penarikan Tabungan oleh Siswa.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function ajukan(Request $request)
    {
        $user = auth()->user();
        $periodeAktif = \App\Helpers\PeriodeHelper::id();

        // 1. Ambil data tabungan spesifik di periode aktif
        $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
            ->where('periode_id', $periodeAktif)
            ->first();

        if (!$tabungan) {
            return back()->with('error', 'Data tabungan Anda pada periode ini tidak ditemukan.');
        }

        $saldo = $tabungan->saldo;

        $validatedData = $request->validate([
            'jumlah_tarik'      => 'required|numeric|min:1000|max:' . $saldo,
            'alasan'            => 'required|string|max:255',
            'jenis_pembayaran'  => 'required|in:Tunai,Digital',
            'metode_digital'    => 'required_if:jenis_pembayaran,Digital',
            'bank_code'         => 'required_if:metode_digital,bank_transfer',
            'nomor_rekening'    => 'required_if:metode_digital,bank_transfer',
            'ewallet_type'      => 'required_if:metode_digital,ewallet',
            'ewallet_number'    => 'required_if:metode_digital,ewallet',
        ], [
            'bank_code.required_if'      => 'Silakan pilih bank',
            'nomor_rekening.required_if' => 'Nomor rekening harus diisi',
            'ewallet_type.required_if'   => 'Silakan pilih e-wallet',
            'ewallet_number.required_if' => 'Nomor e-wallet harus diisi',
            'jumlah_tarik.max'           => 'Jumlah penarikan tidak boleh melebihi saldo saat ini.',
        ]);

        $pengajuan = new \App\Models\Pengajuan();
        $pengajuan->user_id           = $user->id;
        $pengajuan->tabungan_id       = $tabungan->id; // Gunakan id tabungan periode ini
        $pengajuan->jumlah_penarikan  = $validatedData['jumlah_tarik'];
        $pengajuan->alasan            = $validatedData['alasan'];
        $pengajuan->pembayaran        = $validatedData['jenis_pembayaran'];
        $pengajuan->status            = 'Pending';
        $pengajuan->periode_id        = $periodeAktif; // Kunci ke periode aktif

        if ($validatedData['jenis_pembayaran'] === 'Digital') {
            $pengajuan->metode_digital = $validatedData['metode_digital'];

            if ($validatedData['metode_digital'] === 'bank_transfer') {
                $pengajuan->bank_code      = $validatedData['bank_code'];
                $pengajuan->nomor_rekening = $validatedData['nomor_rekening'];
            } elseif ($validatedData['metode_digital'] === 'ewallet') {
                $pengajuan->ewallet_type   = $validatedData['ewallet_type'];
                $pengajuan->ewallet_number = $validatedData['ewallet_number'];
            }
        }

        $pengajuan->save();

        return redirect()->route('siswa.tabungan.tarik')
            ->with('success', 'Pengajuan Penarikan Tabungan Berhasil Diajukan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil Diajukan.')
            ->with('alert-duration', 5000); // Set ke 5 detik agar tidak mengganggu
    }

    public function batal($id)
    {
        $pengajuan = \App\Models\Pengajuan::find($id);

        if (!$pengajuan || $pengajuan->status !== 'Pending') {
            return redirect()->back()->with('error', 'Pengajuan tidak valid atau sudah diproses.');
        }

        $pengajuan->status = 'Batal';
        $pengajuan->save();

        return redirect()->back()
            ->with('success', 'Pengajuan Penarikan Telah Dibatalkan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Pengajuan Penarikan Telah Dibatalkan.')
            ->with('alert-duration', 5000);
    }

    /**
     * Menangani Keputusan Pengajuan Penarikan Tabungan (Tunai & Digital)
     */
    public function terima(Request $request, $id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // 1. Langsung cari berdasarkan ID pengajuan
            $pengajuan = \App\Models\Pengajuan::where('id', $id)
                            ->where('status', 'Pending')
                            ->firstOrFail();

            $user = $pengajuan->user;
            
            // 2. Cari tabungan berdasarkan periode pengajuan tersebut!
            $tabungan = \App\Models\Tabungan::where('user_id', $user->id)
                            ->where('periode_id', $pengajuan->periode_id)
                            ->firstOrFail();

            $jumlahTarikInput = $pengajuan->jumlah_penarikan;
            $biayaAdmin = ceil(($jumlahTarikInput * 0.05) / 1000) * 1000;
            $totalTarik = $jumlahTarikInput + $biayaAdmin;

            if ($totalTarik > $tabungan->saldo) {
                return redirect()->back()->with('error', 'Penarikan (termasuk biaya admin) melebihi saldo tabungan saat ini.');
            }

            if ($pengajuan->pembayaran === 'Digital') {
                // Jika digital, lempar ke Xendit dan status jadi PROCESSING
                $this->processDigitalPayout($pengajuan, $user, $totalTarik);
            } else {
                // Jika Tunai, langsung potong saldo & catat transaksi
                $tabungan->saldo -= $totalTarik;
                $tabungan->save();

                $pengajuan->status = 'Diterima'; // Atau SUCCEEDED
                $pengajuan->save();

                \App\Models\Transaksi::create([
                    'jumlah_transaksi' => $totalTarik,
                    'saldo_awal'       => $tabungan->saldo + $totalTarik,
                    'saldo_akhir'      => $tabungan->saldo,
                    'tipe_transaksi'   => 'Tarik',
                    'pembayaran'       => 'Tunai',
                    'status'           => 'success',
                    'pembuat'          => auth()->user()->name,
                    'user_id'          => $user->id,
                    'tabungan_id'      => $tabungan->id,
                    'periode_id'       => $pengajuan->periode_id // Wajib masuk!
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('bendahara.pengajuan.index')
                ->with('success', 'Pengajuan Penarikan Tabungan berhasil diproses.')
                ->with('alert-type', 'success')
                ->with('alert-message', 'Pengajuan Penarikan Tabungan berhasil diproses.')
                ->with('alert-duration', 5000);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memproses pencairan dana digital ke Xendit V2
     */
    protected function processDigitalPayout($pengajuan, $user, $amount)
    {
        if (!in_array($pengajuan->metode_digital, ['bank_transfer', 'ewallet'])) {
            throw new \Exception('Metode pembayaran digital tidak valid');
        }

        // SET XENDIT KEY (Wajib ada!)
        \Xendit\Configuration::setXenditKey(config('services.xendit.secret_key'));
        $payoutApi = new \Xendit\Payout\PayoutApi();

        $channelProperties = [];
        $referenceId = 'TARIK-' . $pengajuan->id . '-' . time();

        if ($pengajuan->metode_digital === 'bank_transfer') {
            if (empty($pengajuan->bank_code) || empty($pengajuan->nomor_rekening)) {
                throw new \Exception('Data rekening bank tidak lengkap');
            }
            $channelProperties = [
                'account_holder_name' => $user->name,
                'account_number'      => $pengajuan->nomor_rekening,
            ];
            $channelCode = $pengajuan->bank_code;
        } else {
            if (empty($pengajuan->ewallet_type) || empty($pengajuan->ewallet_number)) {
                throw new \Exception('Data e-wallet tidak lengkap');
            }
            $channelProperties = [
                'account_holder_name' => $user->name,
                'account_number'      => $pengajuan->ewallet_number,
            ];
            $channelCode = $pengajuan->ewallet_type;
        }

        $payoutRequest = new \Xendit\Payout\CreatePayoutRequest([
            'reference_id'       => $referenceId,
            'currency'           => 'IDR',
            'channel_code'       => $channelCode,
            'channel_properties' => $channelProperties,
            'amount'             => $amount,
            'description'        => 'Penarikan Tabungan Siswa ' . $user->name,
            'type'               => 'DIRECT_DISBURSEMENT' // Atau bebas sesuai kebutuhan API V2
        ]);

        // Eksekusi Payout V2
        $idempotencyKey = 'DISB-' . \Illuminate\Support\Str::random(10);
        $result = $payoutApi->createPayout($idempotencyKey, null, $payoutRequest);

        $pengajuan->xendit_payout_id = $result['id'];
        $pengajuan->status = 'PROCESSING';
        $pengajuan->reference_id = $referenceId;
        $pengajuan->save();

        return $result;
    }

    /**
     * Menangani webhook dari Xendit Payouts V2
     */
    public function handlePayoutWebhook(Request $request)
    {
        // Proteksi CSRF/Callback Token (Sangat disarankan)
        $xenditXCallbackToken = $request->header('x-callback-token');
        if ($xenditXCallbackToken !== config('services.xendit.webhook_token')) {
            return response()->json(['message' => 'Unauthorized Webhook Token'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if (!isset($data['reference_id'])) {
            return response()->json(['message' => 'Reference ID is missing'], 400);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $pengajuan = \App\Models\Pengajuan::where('reference_id', $data['reference_id'])->first();

            if (!$pengajuan) {
                throw new \Exception('Pengajuan tidak ditemukan.');
            }

            if ($pengajuan->status !== 'PROCESSING') {
                return response()->json(['message' => 'Pengajuan sudah diproses sebelumnya.'], 200);
            }

            // Jika status Payout BERHASIL
            if ($event === 'payout.succeeded') {
                $pengajuan->status = 'SUCCEEDED';
                $pengajuan->save();

                // Potong saldo tabungan (Total termasuk admin!)
                $tabungan = \App\Models\Tabungan::where('id', $pengajuan->tabungan_id)->firstOrFail();
                $saldo_sebelum = $tabungan->saldo;
                
                $jumlahTarikInput = $pengajuan->jumlah_penarikan;
                $biayaAdmin = ceil(($jumlahTarikInput * 0.05) / 1000) * 1000;
                $totalTarik = $jumlahTarikInput + $biayaAdmin;

                $tabungan->saldo -= $totalTarik;
                $tabungan->save();

                \App\Models\Transaksi::create([
                    'jumlah_transaksi' => $totalTarik,
                    'saldo_awal'       => $saldo_sebelum,
                    'saldo_akhir'      => $tabungan->saldo,
                    'tipe_transaksi'   => 'Tarik',
                    'pembayaran'       => 'Digital',
                    'status'           => 'success',
                    'pembuat'          => 'Xendit',
                    'token_stor'       => $data['id'] ?? null,
                    'user_id'          => $pengajuan->user_id,
                    'tabungan_id'      => $tabungan->id,
                    'periode_id'       => $pengajuan->periode_id // Wajib masuk!
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['message' => 'Payout processed successfully'], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Webhook Payout Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error processing payout'], 500);
        }
    }

    // Bendahara Tolak Pengajuan ----------------------------------------------------------------------------------
    /**
    * Menolak pengajuan penarikan tabungan siswa oleh Bendahara.
    *
    * @param int $id ID user yang mengajukan penarikan
    * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar pengajuan dengan notifikasi
    */

    public function tolak($id)
    {
        $pengajuan = \App\Models\Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'Pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pengajuan->status = 'Tolak';
        $pengajuan->save();

        // Gunakan redirect back agar kembali ke halaman filter sebelumnya dengan aman
        return redirect()->back()
            ->with('success', 'Pengajuan Penarikan Tabungan berhasil ditolak.')
            ->with('alert-type', 'danger')
            ->with('alert-message', 'Pengajuan Penarikan Tabungan berhasil ditolak.')
            ->with('alert-duration', 5000);
    }
    /**
    * Mengambil daftar channel pembayaran yang tersedia dari Xendit.
    *
    * Fungsi ini menghubungi API Xendit untuk mendapatkan daftar metode pembayaran
    * yang dapat digunakan untuk melakukan pencairan dana (payout), seperti transfer bank atau e-wallet.
    *
    * @return \Illuminate\Http\JsonResponse Daftar channel pembayaran dalam format JSON
    */

    public function getPayoutChannels()
    {
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));

        $apiInstance = new PayoutApi();

        $currency = "IDR";
        $channel_category = null;
        $channel_code = null;
        $for_user_id = null;

        try {
            $result = $apiInstance->getPayoutChannels($currency, $channel_category, $channel_code, $for_user_id);

            return response()->json($result);
        } catch (\Xendit\XenditSdkException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'full_error' => $e->getFullError()
            ], 500);
        }
    }
}
