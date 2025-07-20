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
        $perPage = request('perPage', 10);

        $query = Pengajuan::query()->where('status', 'pending');
        $query->select('id', 'jumlah_penarikan', 'status', 'pembayaran', 'alasan', 'user_id', 'tabungan_id', 'created_at');
        $searchTerm = $request->input('search');

        if (!empty($searchTerm)) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('jumlah_penarikan', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('status', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('pembayaran', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('alasan', 'LIKE', '%'.$searchTerm.'%');
            });

            $query->orWhereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%'.$searchTerm.'%');
            });

            $query->orWhereHas('user.kelas', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        $query->orderBy('created_at', 'desc');
        $pengajuan = $query->paginate($perPage);

        // dd($pengajuan);
        return view('bendahara.kelola_pengajuan', compact('pengajuan'));
    }

    /**
    * Mengambil data pengajuan berdasarkan ID dan mengembalikannya dalam format JSON.
    *
    * @param int $id ID pengajuan yang akan diambil.
    * @return \Illuminate\Http\JsonResponse Response JSON yang berisi data pengajuan.
    */

    public function getPengajuanData($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        return response()->json([
            'id' => $pengajuan->user->id ?? '-',
            'jumlah_tarik' => $pengajuan->jumlah_penarikan ?? '-',
            'username' => $pengajuan->user->username ?? '-',
            'status' => $pengajuan->status ?? '-',
            'pembayaran' => $pengajuan->pembayaran ?? '-',
            'metode_digital' => $pengajuan->metode_digital ?? '-',
            'type_tujuan' => $pengajuan->bank_code ?? $pengajuan->ewallet_type ?? '-',
            'nomor_tujuan' => $pengajuan->nomor_rekening ?? $pengajuan->ewallet_number ?? '-',
            'name' => $pengajuan->user->name ?? '-',
            'kelas' => $pengajuan->user->kelas->name ?? '-',
            'tabungan' => $pengajuan->user->tabungan->saldo ?? '-',
            'alasan' => $pengajuan->alasan ?? '-',
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
        $saldo = $user->tabungan->saldo;

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
        ]);

        $pengajuan = new Pengajuan();
        $pengajuan->user_id           = $user->id;
        $pengajuan->tabungan_id       = $user->tabungan->id;
        $pengajuan->jumlah_penarikan  = $validatedData['jumlah_tarik'];
        $pengajuan->alasan            = $validatedData['alasan'];
        $pengajuan->pembayaran        = $validatedData['jenis_pembayaran'];
        $pengajuan->status            = 'Pending';

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
            ->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Ajukan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Ajukan.')
            ->with('alert-duration', 30000);
    }



    public function batal($id)
    {
        $pengajuan = Pengajuan::find($id);

        if (!$pengajuan) {
            return redirect()->back()->with('error', 'Pengajuan tidak ditemukan.');
        }

        $pengajuan->status = 'Batal';
        $pengajuan->save();

        return redirect()->back()->with('success', 'Pengajuan Penarikan Telah di Batalkan.')
        ->with('alert-type', 'success')
        ->with('alert-message', 'Pengajuan Penarikan Telah di Batalkan.')
        ->with('alert-duration', 30000);
    }

    // Bendahara Terima Pengajuan ----------------------------------------------------------------------------------
    /**
    * Menangani Keputusan Pengajuan Penarikan Tabungan Siswa oleh Bendahara.
    *
    * @param Request $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function terima(Request $request)
    {
        $validatedData = $request->validate([
            'username'      => 'required',
            'jumlah_tarik'  => 'required|numeric|min:10000',
            'alasan'        => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::where('username', $validatedData['username'])->firstOrFail();
            $pengajuan = Pengajuan::where('user_id', $user->id)->where('status', 'Pending')->firstOrFail();
            $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

            $jumlahTarik = $validatedData['jumlah_tarik'];
            $biayaAdmin = ceil(($jumlahTarik * 0.05) / 1000) * 1000;

            if ($jumlahTarik > $tabungan->saldo) {
                return redirect()->back()
                    ->withErrors(['jumlah_tarik' => 'Penarikan melebihi saldo tabungan'])
                    ->withInput();
            }

            $pengajuan->status = 'Diterima';
            $pengajuan->premi = $biayaAdmin;
            $pengajuan->save();

            if ($pengajuan->pembayaran === 'Digital') {
                $this->processDigitalPayout($pengajuan, $user, $jumlahTarik);
            }

            DB::commit();

            return redirect()->route('bendahara.pengajuan.index')
                ->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Terima.')
                ->with('alert-type', 'success')
                ->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Terima.')
                ->with('alert-duration', 30000);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
    * Memproses pencairan dana digital untuk pengajuan tabungan siswa.
    *
    * @param Pengajuan $pengajuan Data pengajuan tabungan yang akan diproses.
    * @param User $user Pengguna yang mengajukan pencairan.
    * @param float $amount Jumlah dana yang akan dicairkan.
    * @return mixed Hasil dari permintaan pencairan dana melalui Xendit.
    * @throws \Exception Jika metode pembayaran tidak valid atau data pembayaran tidak lengkap.
    */

    protected function processDigitalPayout($pengajuan, $user, $amount)
    {
        if (!in_array($pengajuan->metode_digital, ['bank_transfer', 'ewallet'])) {
            throw new \Exception('Metode pembayaran digital tidak valid');
        }

        $channelProperties = [];
        $referenceId = 'SISWA-' . $user->id . '-' . now()->format('YmdHis');

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
                'account_number' => $pengajuan->ewallet_number,
            ];
            $channelCode = $pengajuan->ewallet_type;
        }

        $payoutRequest = new CreatePayoutRequest([
            'reference_id'       => $referenceId,
            'currency'           => 'IDR',
            'channel_code'       => $channelCode,
            'channel_properties' => $channelProperties,
            'amount'             => $amount,
            'description'        => 'Penarikan Tabungan Siswa ' . $user->name,
            'type'               => 'DIRECT_DISBURSEMENT'
        ]);

        $result = $this->payoutApi->createPayout(
            'DISB-' . Str::random(10),
            null,
            $payoutRequest
        );

        $pengajuan->xendit_payout_id = $result->getId();
        $pengajuan->status = 'PROCESSING';
        $pengajuan->reference_id = $referenceId;
        $pengajuan->save();

        return $result;
    }

    /**
    * Memeriksa status pencairan dana (payout) berdasarkan ID payout yang diberikan.
    *
    * @param string $payoutId ID dari payout yang ingin diperiksa statusnya.
    * @return mixed Status payout jika berhasil, atau null jika terjadi kesalahan.
    */

    public function checkPayoutStatus($payoutId)
    {
        try {
            $payoutStatus = $this->payoutApi->getPayoutById($payoutId);
            return $payoutStatus;
        } catch (\Xendit\XenditSdkException $e) {
            \Log::error('Payout Status Check Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
    * Menangani webhook dari Xendit untuk payout yang berhasil.
    *
    * @param Request $request Data yang dikirimkan oleh Xendit melalui webhook.
    * @return \Illuminate\Http\JsonResponse Respon JSON dengan status sukses atau error.
    */

    public function handlePayoutWebhook(Request $request)
    {
        Log::info('Webhook Payout Diterima', ['payload' => $request->all()]);

        $data = $request->input('data');

        if ($request->input('event') !== 'payout.succeeded') {
            return response()->json(['message' => 'Event not processed'], 400);
        }

        if (!isset($data['reference_id'])) {
            return response()->json(['message' => 'Reference ID is missing'], 400);
        }

        DB::beginTransaction();
        try {
            $pengajuan = Pengajuan::where('reference_id', $data['reference_id'])->first();

            if (!$pengajuan) {
                throw new \Exception('Pengajuan tidak ditemukan untuk reference_id: ' . $data['reference_id']);
            }

            if ($pengajuan->status !== 'PROCESSING') {
                throw new \Exception('Status pengajuan bukan PROCESSING, tidak dapat diperbarui.');
            }

            $pengajuan->status = 'SUCCEEDED';
            $pengajuan->xendit_payout_id = $data['id'];
            $pengajuan->save();

            $tabungan = Tabungan::where('user_id', $pengajuan->user_id)->firstOrFail();
            $saldo_sebelum = $tabungan->saldo;
            $tabungan->saldo -= $pengajuan->jumlah_penarikan;
            $tabungan->premi = $tabungan->saldo * 0.025;
            $tabungan->sisa = $tabungan->saldo - $tabungan->premi;
            $tabungan->save();

            Transaksi::create([
                'jumlah_transaksi' => $pengajuan->jumlah_penarikan,
                'saldo_awal' => $saldo_sebelum,
                'saldo_akhir' => $tabungan->saldo,
                'tipe_transaksi' => 'Tarik',
                'pembayaran' => $pengajuan->pembayaran,
                'status' => 'success',
                'pembuat' => 'Xendit Webhook',
                'token_stor' => $data['idempotency_key'] ?? null,
                'user_id' => $pengajuan->user_id,
                'tabungan_id' => $tabungan->id
            ]);

            DB::commit();

            return response()->json(['message' => 'Payout processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook Payout Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error processing payout', 'error' => $e->getMessage()], 500);
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
        $pengajuan = Pengajuan::where('user_id', $id)->latest()->first();

        $pengajuan->status = 'Tolak';

        $pengajuan->save();

        return redirect()->route('bendahara.pengajuan.index')->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Tolak.')->with('alert-type', 'danger')->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Tolak.')->with('alert-duration', 30000);
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
