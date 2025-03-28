<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use Xendit\Configuration;
use Illuminate\Support\Str;
use Xendit\Payout\PayoutApi;
use Illuminate\Http\Request;
use App\Helpers\RupiahHelper;
use Illuminate\Support\Facades\DB;
use Xendit\Payout\CreatePayoutRequest;

class PengajuanController extends Controller
{
    // Bendahara Kelola Pengajuan --------------------------------------------------------------------------------
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

    public function getPengajuanData($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        return response()->json([
            'id' => $pengajuan->user->id ?? '-',
            'username' => $pengajuan->user->username ?? '-',
            'name' => $pengajuan->user->name ?? '-',
            'kelas' => $pengajuan->user->kelas->name ?? '-',
            'tabungan' => $pengajuan->user->tabungan->saldo ?? '-',
            'alasan' => $pengajuan->alasan ?? '-',
            'jumlah_tarik' => $pengajuan->jumlah_penarikan ?? '-',
            'pembayaran' => $pengajuan->pembayaran ?? '-',
        ]);
    }

    // Siswa Ajukan Penarikan Tabungan ---------------------------------------------------------------------------
    public function ajukan(Request $request)
{
    $validatedData = $request->validate([
        'jumlah_tarik' => 'required|numeric|min:1000|max:' . auth()->user()->tabungan->saldo,
        'alasan' => 'required|string|max:255',
        'jenis_pembayaran' => 'required|in:Tunai,Digital',
        // Kondisional validasi untuk metode digital
        'metode_digital' => 'required_if:jenis_pembayaran,Digital',
        'bank_code' => 'required_if:metode_digital,bank_transfer',
        'nomor_rekening' => 'required_if:metode_digital,bank_transfer',
        'ewallet_type' => 'required_if:metode_digital,ewallet',
        'ewallet_number' => 'required_if:metode_digital,ewallet',
    ], [
        // Tambahkan pesan error yang sesuai
        'bank_code.required_if' => 'Silakan pilih bank',
        'nomor_rekening.required_if' => 'Nomor rekening harus diisi',
        'ewallet_type.required_if' => 'Silakan pilih e-wallet',
        'ewallet_number.required_if' => 'Nomor e-wallet harus diisi',
    ]);

    $pengajuan = new Pengajuan();
    $pengajuan->user_id = auth()->user()->id;
    $pengajuan->tabungan_id = auth()->user()->tabungan->id;
    $pengajuan->jumlah_penarikan = $validatedData['jumlah_tarik'];
    $pengajuan->alasan = $validatedData['alasan'];
    $pengajuan->pembayaran = $validatedData['jenis_pembayaran'];
    $pengajuan->status = 'Pending';

    // Simpan detail pembayaran digital jika dipilih
    if ($validatedData['jenis_pembayaran'] === 'Digital') {
        $pengajuan->metode_digital = $validatedData['metode_digital'];

        if ($validatedData['metode_digital'] === 'bank_transfer') {
            $pengajuan->bank_code = $validatedData['bank_code'];
            $pengajuan->nomor_rekening = $validatedData['nomor_rekening'];
        } elseif ($validatedData['metode_digital'] === 'ewallet') {
            $pengajuan->ewallet_type = $validatedData['ewallet_type'];
            $pengajuan->ewallet_number = $validatedData['ewallet_number'];
        }
    }

    $pengajuan->save();

    return redirect()->route('siswa.tabungan.tarik')->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Ajukan.')->with('alert-type', 'success')->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Ajukan.')->with('alert-duration', 30000);
}

    // Bendahara Terima Pengajuan ----------------------------------------------------------------------------------
    public function terima(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required',
            'kelas' => 'required|string|max:255',
            'tabungan' => 'required|numeric',
            'jumlah_tarik' => 'required',
            'pembayaran' => 'required|string|max:255',
            'alasan' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tarik.required' => 'Jumlah penarikan harus diisi.',
            'pembayaran.required' => 'Pembayaran harus diisi.',
            'alasan.required' => 'Alasan harus diisi.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();
        $pengajuan = Pengajuan::where('user_id', $user->id)->firstOrFail();
        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        // Validate withdrawal amount
        if ($validatedData['jumlah_tarik'] > $tabungan->saldo) {
            return redirect()->back()->withErrors(['jumlah_tarik' => 'Penarikan tabungan melebihi saldo tabungan'])->withInput();
        }
        if ($pengajuan->pembayaran === 'Digital') {
            try {

                if ($pengajuan->metode_digital === 'bank_transfer') {
                    // Payout ke rekening bank
                    $payoutRequest = new CreatePayoutRequest([
                        'reference_id' => 'SISWA-' . $pengajuan->user_id . '-' . Str::random(6),
                        'currency' => 'IDR',
                        'channel_code' => $pengajuan->bank_code,
                        'channel_properties' => [
                            'account_holder_name' => $pengajuan->user->name,
                            'account_number' => $pengajuan->nomor_rekening
                        ],
                        'amount' => $pengajuan->jumlah_penarikan,
                        'description' => 'Penarikan Tabungan Siswa',
                        'type' => 'DIRECT_DISBURSEMENT'
                    ]);
                } elseif ($pengajuan->metode_digital === 'ewallet') {
                    // Payout ke e-wallet
                    $payoutRequest = new CreatePayoutRequest([
                        'reference_id' => 'SISWA-' . $pengajuan->user_id . '-' . Str::random(6),
                        'currency' => 'IDR',
                        'channel_code' => $pengajuan->ewallet_type,
                        'channel_properties' => [
                            'phone_number' => $pengajuan->ewallet_number
                        ],
                        'amount' => $pengajuan->jumlah_penarikan,
                        'description' => 'Penarikan Tabungan Siswa',
                        'type' => 'DIRECT_DISBURSEMENT'
                    ]);
                }

                // Lakukan payout Xendit
                $result = $this->payoutApi->createPayout(
                    'DISB-' . Str::random(10),
                    null,
                    $payoutRequest
                );

                // Simpan Xendit Payout ID
                $pengajuan->xendit_payout_id = $result->getId();

                // Update pengajuan status
                $pengajuan->status = 'Terima';

                // Create transaction record
                $transaksi = new Transaksi();
                $transaksi->jumlah_transaksi = $validatedData['jumlah_tarik'];
                $transaksi->saldo_awal = $tabungan->saldo;
                $transaksi->saldo_akhir = $tabungan->saldo - $validatedData['jumlah_tarik'];
                $transaksi->tipe_transaksi = 'Tarik';
                $transaksi->pembayaran = $validatedData['pembayaran'];
                $transaksi->status = 'success';
                $transaksi->pembuat = auth()->user()->name;
                $transaksi->token_stor = Str::random(10);
                $transaksi->user_id = $user->id;
                $transaksi->tabungan_id = $tabungan->id;

                // Update tabungan
                $tabungan->saldo = $transaksi->saldo_akhir;
                $tabungan->premi = $tabungan->saldo / 100 * 2.5;
                $tabungan->sisa = $tabungan->saldo - $tabungan->premi;

                // Save all records
                $transaksi->save();
                $tabungan->save();
                $pengajuan->save();

                return redirect()->route('bendahara.pengajuan.index')
                    ->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Terima.')
                    ->with('alert-type', 'success')
                    ->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Terima.')
                    ->with('alert-duration', 30000);

            } catch (\Xendit\XenditSdkException $e) {
                // Handle Xendit API errors
                return redirect()->back()
                    ->withErrors(['pembayaran' => 'Gagal melakukan payout: ' . $e->getMessage()])
                    ->withInput();
            }
        }
    }

    // Additional method to check payout status
    public function checkPayoutStatus($payoutId)
    {
        try {
            $payoutStatus = $this->payoutApi->getPayoutById($payoutId);
            return $payoutStatus;
        } catch (\Xendit\XenditSdkException $e) {
            // Log the error
            \Log::error('Payout Status Check Error: ' . $e->getMessage());
            return null;
        }
    }

    // Bendahara Tolak Pengajuan ----------------------------------------------------------------------------------
    public function tolak($id)
    {
        $pengajuan = Pengajuan::where('user_id', $id)->latest()->first();

        $pengajuan->status = 'Tolak';

        $pengajuan->save();

        return redirect()->route('bendahara.pengajuan.index')->with('success', 'Pengajuan Penarikan Tabungan Berhasil di Tolak.')->with('alert-type', 'danger')->with('alert-message', 'Pengajuan Penarikan Tabungan Berhasil di Tolak.')->with('alert-duration', 30000);
    }

    public function getPayoutChannels()
    {
        // Set API Key
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));

        // Inisialisasi API
        $apiInstance = new PayoutApi();

        // Parameter opsional (kosongkan jika tidak ingin menggunakan filter)
        $currency = "IDR"; // Bisa "IDR" atau "PHP"
        $channel_category = null; // Bisa diisi kategori seperti "BANK" atau "EWALLET"
        $channel_code = null; // Bisa diisi kode bank atau e-wallet tertentu
        $for_user_id = null; // Jika tidak menggunakan sub-account, bisa dikosongkan

        try {
            // Panggil API untuk mendapatkan daftar payout channels
            $result = $apiInstance->getPayoutChannels($currency, $channel_category, $channel_code, $for_user_id);

            // Kembalikan response dalam format JSON
            return response()->json($result);
        } catch (\Xendit\XenditSdkException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'full_error' => $e->getFullError()
            ], 500);
        }
    }

}
