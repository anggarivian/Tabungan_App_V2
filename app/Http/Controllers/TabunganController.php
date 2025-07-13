<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Xendit\Xendit;
use Xendit\Invoice;
use App\Models\User;
use Xendit\Invoices;
use App\Models\Kelas;
use App\Models\Tabungan;
use App\Models\Pengajuan;
use App\Models\Transaksi;
use Xendit\Configuration;
use Midtrans\Notification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\RupiahHelper;
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


class TabunganController extends Controller
{
    /**
    * Konstruktor untuk menginisialisasi konfigurasi API Xendit.
    */
    private $invoiceClient;

    public function __construct()
    {
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    // Bendahara ------------------------------------------------------------------------------------------------------------------------------------------------
    /**
    * Menampilkan Dashboard Tabungan oleh Bendahara.
    *
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_index()
    {
        $perPage = request('perPage', 10);
        $kelasList = [];

        for ($i = 1; $i <= 6; $i++) {
            $transaksi = Transaksi::whereHas('user.kelas', function ($query) use ($i) {
                    $query->where('kelas_id', $i);
                })
                ->whereDate('created_at', Carbon::today())
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $stor = Transaksi::whereHas('user.kelas', function ($query) use ($i) {
                    $query->where('kelas_id', $i);
                })
                ->where('tipe_transaksi', 'Stor')
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->sum('jumlah_transaksi');

            $tarik = Transaksi::whereHas('user.kelas', function ($query) use ($i) {
                    $query->where('kelas_id', $i);
                })
                ->where('tipe_transaksi', 'Tarik')
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->sum('jumlah_transaksi');

            $kelasList["kelas$i"] = [
                'data' => $transaksi,
                'stor' => $stor,
                'tarik' => $tarik,
            ];
        }

        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->whereDate('created_at', Carbon::today())
            ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->whereDate('created_at', Carbon::today())
            ->sum('jumlah_transaksi');

        $jumlah_saldo_tunai = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->where('pembayaran', 'Tunai')
            ->whereDate('created_at', Carbon::today())
            ->sum('jumlah_transaksi')
            - Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->where('pembayaran', 'Tunai')
            ->whereDate('created_at', Carbon::today())
            ->sum('jumlah_transaksi');

        $jumlah_saldo_digital = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->where('pembayaran', 'Digital')
            ->whereDate('created_at', Carbon::today())
            ->sum('jumlah_transaksi')
            - Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->where('pembayaran', 'Digital')
            ->whereDate('created_at', Carbon::today())
            ->sum('jumlah_transaksi');

        return view('bendahara.tabungan.index', compact('transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo_tunai', 'jumlah_saldo_digital', 'kelasList'));
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
    * Menampilkan Dashboard Stor Tabungan Massal / Per Kelas oleh Bendahara.
    *
    * @param $id ID Kelas
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_storMasal($id)
    {
        $kelas = Kelas::find($id);
        $siswa = User::where('kelas_id', $id)->where('roles_id', 4)->get();
        $walikelas = User::where('kelas_id', $id)->where('roles_id', 3)->first();
        $kemarin = Carbon::yesterday()->toDateString();

        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->whereHas('user', function ($query) use ($id) {
                $query->where('kelas_id', $id);
            })
            ->where('status', 'success')
            ->sum('jumlah_transaksi');


        return view('bendahara.tabungan.stor_masal', compact('kelas', 'siswa', 'walikelas', 'jumlahTransaksiKemarin'));
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
        $username = $request->get('username');

        $user = User::where('username', $username)->first();

        if ($user) {
            $user = $user->fresh(['tabungan', 'kelas']);

            return response()->json([
                'name' => $user->name ?? 'Tidak Ada',
                'kelas' => $user->kelas->name ?? 'Tidak Ada',
                'tabungan' => number_format($user->tabungan->saldo, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'name' => 'Tidak Ada',
            'kelas' => 'Tidak Ada',
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
            'jumlah_stor' => str_replace('.', '', $request->jumlah_stor)
        ]);

        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_stor' => 'required|numeric|min:1000',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric' => 'Jumlah stor harus berupa angka.',
            'jumlah_stor.min' => 'Minimal stor adalah 1.000.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();
        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $jumlah_stor = $validatedData['jumlah_stor'];

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $jumlah_stor;
        $transaksi->saldo_awal = $tabungan->saldo;
        $transaksi->saldo_akhir = $tabungan->saldo + $jumlah_stor;
        $transaksi->tipe_transaksi = 'Stor';
        $transaksi->pembayaran = 'Tunai';
        $transaksi->status = 'success';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = Str::random(10);
        $transaksi->user_id = $user->id;
        $transaksi->tabungan_id = $tabungan->id;

        $tabungan->saldo = $transaksi->saldo_akhir;
        $tabungan->premi = $transaksi->saldo_akhir * 2.5 / 100;
        $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi;

        // Kirim WhatsApp
        try {
            // Normalisasi nomor: strip non-digit, format E.164
            $digits = preg_replace('/\D+/', '', $user->kontak);
            if (Str::startsWith($digits, '0')) {
                $digits = '62'.substr($digits, 1);
            } elseif (Str::startsWith($digits, '8')) {
                $digits = '62'.$digits;
            }

            // Format angka
            $fmt  = fn($n) => number_format($n, 0, ',', '.');
            $msg  = "Halo, {$user->name} 👋\n\n";
            $msg .= "*Stor Tabungan* Anda Berhasil:\n\n";
            $msg .= "🔹 Saldo Awal : Rp " . $fmt($transaksi->saldo_awal) . "\n";
            $msg .= "🔹 *Jumlah Stor : Rp " . $fmt($transaksi->jumlah_transaksi) . "*\n";
            $msg .= "🔹 Saldo Akhir : Rp " . $fmt($transaksi->saldo_akhir) . "\n\n";
            $msg .= "🙏 Terima kasih.\n";
            $msg .= "_Cek detail: sukarame-tabungan-siswa.my.id_\n";
            $msg .= "⚠️ _Pesan otomatis. Jangan dibalas._";

            $token     = config('services.wablas.token');
            $secret    = config('services.wablas.secret_key');
            $endpoint  = config('services.wablas.endpoint');
            $payload   = ['data' => [[
                'phone'   => $digits,
                'message' => $msg,
                'isGroup' => 'false',
            ]]];

            $response = Http::retry(3, 100)
                ->withHeaders([
                    'Authorization' => "{$token}.{$secret}",
                    'Content-Type'  => 'application/json',
                ])
                ->post($endpoint, $payload);

            if (! $response->ok() || ! data_get($response->json(), 'status')) {

                Log::error('WA gagal: '. $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error kirim WA: '. $e->getMessage());
        }

        $transaksi->save();
        $tabungan->save();

        return redirect()->back()
            ->with('success', 'Tabungan berhasil disimpan')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Tabungan berhasil disimpan')
            ->with('alert-duration', 3000);
    }

    /**
    * Menyimpan stor tabungan siswa secara masal / perkelas oleh Bendahara.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_storMasalTabungan(Request $request)
    {
        $items = $request->input('input', []);

        foreach ($items as $index => $data) {
            $stor = str_replace([',', '.'], '', $data['stor']);
            if (! is_numeric($stor) || (int)$stor < 1000) {
                continue;
            }

            $jumlahStor = (int)$stor;
            $user = User::where('username', $data['username'])->first();
            if (! $user || ! $user->tabungan) {
                continue;
            }

            $saldoAwal  = $user->tabungan->saldo;
            $saldoAkhir = $saldoAwal + $jumlahStor;

            $tabungan = $user->tabungan;
            $tabungan->saldo = $saldoAkhir;
            $tabungan->premi = $saldoAkhir * 0.025;
            $tabungan->sisa  = $saldoAkhir - $tabungan->premi;
            $tabungan->save();

            $transaksi = Transaksi::create([
                'user_id'            => $user->id,
                'tabungan_id'        => $tabungan->id,
                'jumlah_transaksi'   => $jumlahStor,
                'saldo_awal'         => $saldoAwal,
                'saldo_akhir'        => $saldoAkhir,
                'tipe_transaksi'     => 'Stor',
                'pembayaran'         => 'Tunai',
                'status'             => 'success',
                'pembuat'            => auth()->user()->name,
                'token_stor'         => Str::random(10),
            ]);

            // Kirim WhatsApp langsung di dalam fungsi dengan delay 15 detik per orang
            try {
                $digits = preg_replace('/\D+/', '', $user->kontak);
                if (Str::startsWith($digits, '0')) {
                    $digits = '62'.substr($digits, 1);
                } elseif (Str::startsWith($digits, '8')) {
                    $digits = '62'.$digits;
                }

                $fmt = fn($n) => number_format($n, 0, ',', '.');
                $msg = "Halo, {$user->name} 👋\n\n";
                $msg .= "*Stor Tabungan* Anda Berhasil:\n\n";
                $msg .= "🔹 Saldo Awal : Rp " . $fmt($transaksi->saldo_awal) . "\n";
                $msg .= "🔹 *Jumlah Stor : Rp " . $fmt($transaksi->jumlah_transaksi) . "*\n";
                $msg .= "🔹 Saldo Akhir : Rp " . $fmt($transaksi->saldo_akhir) . "\n\n";
                $msg .= "🙏 Terima kasih.\n";
                $msg .= "_Cek detail: sukarame-tabungan-siswa.my.id_\n";
                $msg .= "⚠️ _Pesan otomatis. Jangan dibalas._";

                $token    = config('services.wablas.token');
                $secret   = config('services.wablas.secret_key');
                $endpoint = config('services.wablas.endpoint');
                $payload  = ['data' => [[
                    'phone'   => $digits,
                    'message' => $msg,
                    'isGroup' => 'false',
                ]]];

                $response = Http::withHeaders([
                    'Authorization' => "{$token}.{$secret}",
                    'Content-Type'  => 'application/json',
                ])->post($endpoint, $payload);

                if (! $response->ok() || ! data_get($response->json(), 'status')) {
                    Log::error('WA gagal: ' . $response->body());
                }

                sleep(15); // delay 15 detik antar pesan sesuai limit paket

            } catch (\Exception $e) {
                Log::error('Error kirim WA: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-duration', 3000);
    }

    /**
    * Menyimpan tarik tabungan siswa oleh Bendahara.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function bendahara_tarikTabungan(Request $request)
    {
        $request->merge([
            'premi' => str_replace('.', '', $request->premi),
            'jumlah_tarik' => str_replace('.', '', $request->jumlah_tarik)
        ]);

        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_tabungan' => 'required|numeric|min:0',
            'premi' => 'required|numeric|min:1000',
            'jumlah_tarik' => 'required|numeric|min:9999',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric' => 'Jumlah tabungan harus berupa angka.',
            'premi.required' => 'Jumlah premi harus diisi.',
            'jumlah_tarik.required' => 'Jumlah tarik harus diisi.',
            'jumlah_tarik.numeric' => 'Jumlah tarik harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();
        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();
        $bendahara = Tabungan::where('user_id', 2 )->firstOrFail();

        if ($validatedData['jumlah_tarik'] > $tabungan->saldo) {
            return redirect()->back()
                ->withErrors(['jumlah_tarik' => 'Penarikan tabungan melebihi saldo tabungan'])
                ->withInput();
        }

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_tarik'] + $validatedData['premi'];
        $transaksi->saldo_awal = $tabungan->saldo;
        $transaksi->saldo_akhir = $tabungan->saldo - $validatedData['jumlah_tarik'] - $validatedData['premi'];
        $transaksi->tipe_transaksi = 'Tarik';
        $transaksi->pembayaran = 'Tunai';
        $transaksi->status = 'success';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id;
        $transaksi->tabungan_id = $tabungan->id;

        // Update tabungan
        $tabungan->saldo = $transaksi->saldo_akhir;
        $tabungan->premi = $tabungan->saldo / 100 * 2.5;
        $tabungan->sisa = $tabungan->saldo - $tabungan->premi;

        $bendahara->saldo = $bendahara->saldo + $validatedData['premi'];

        // Kirim WhatsApp
        try {
            // Normalisasi nomor: strip non-digit, format E.164
            $digits = preg_replace('/\D+/', '', $user->kontak);
            if (Str::startsWith($digits, '0')) {
                $digits = '62'.substr($digits, 1);
            } elseif (Str::startsWith($digits, '8')) {
                $digits = '62'.$digits;
            }

            // Format angka
            $fmt  = fn($n) => number_format($n, 0, ',', '.');
            $msg  = "Halo, {$user->name} 👋\n\n";
            $msg .= "*Tarik Tabungan* Anda Berhasil:\n\n";
            $msg .= "🔹 Saldo Awal : Rp " . $fmt($transaksi->saldo_awal) . "\n";
            $msg .= "🔹 *Jumlah Stor : Rp " . $fmt($transaksi->jumlah_transaksi) . "*\n";
            $msg .= "🔹 Saldo Akhir : Rp " . $fmt($transaksi->saldo_akhir) . "\n\n";
            $msg .= "🙏 Terima kasih.\n";
            $msg .= "_Cek detail: sukarame-tabungan-siswa.my.id_\n";
            $msg .= "⚠️ _Pesan otomatis. Jangan dibalas._";

            $token     = config('services.wablas.token');
            $secret    = config('services.wablas.secret_key');
            $endpoint  = config('services.wablas.endpoint');
            $payload   = ['data' => [[
                'phone'   => $digits,
                'message' => $msg,
                'isGroup' => 'false',
            ]]];

            $response = Http::retry(3, 100)
                ->withHeaders([
                    'Authorization' => "{$token}.{$secret}",
                    'Content-Type'  => 'application/json',
                ])
                ->post($endpoint, $payload);

            if (! $response->ok() || ! data_get($response->json(), 'status')) {

                Log::error('WA gagal: '. $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error kirim WA: '. $e->getMessage());
        }

        $transaksi->save();
        $tabungan->save();
        $bendahara->save();

        $user = $user->fresh(['tabungan']);

        return redirect()->back()
            ->with('success', 'Tabungan berhasil ditarik')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Tabungan berhasil ditarik')
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
        ->where('status', 'success')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->where('status', 'success')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $jumlah_saldo_tunai = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Stor')
        ->where('status', 'success')
        ->where('pembayaran', 'Tunai')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi')
        - Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->where('status', 'success')
        ->where('pembayaran', 'Tunai')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $jumlah_saldo_digital = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Stor')
        ->where('status', 'success')
        ->where('pembayaran', 'Digital')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi')
        - Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->where('status', 'success')
        ->where('pembayaran', 'Digital')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $kelas = Transaksi::whereHas('user.kelas', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->whereDate('created_at', Carbon::today())
        ->where('status', 'success')
        ->paginate($perPage);


        return view('walikelas.tabungan.index', compact('transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo_tunai', 'jumlah_saldo_digital', 'kelas'));
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
        $siswa = User::where('kelas_id', $kelas->id)->where('roles_id', 4)->get();
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

        $user = DB::table('users')
            ->join('tabungans', 'tabungans.user_id', '=', 'users.id')
            ->join('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->where('users.username', $username)
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
                'tabungan' => number_format($user->tabungan_saldo, 0, ',', ','),
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
            'jumlah_stor' => str_replace('.', '', $request->jumlah_stor)
        ]);

        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_stor' => 'required|numeric|min:1000',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric' => 'Jumlah stor harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();
        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_stor'];
        $transaksi->saldo_awal = $tabungan->saldo;
        $transaksi->saldo_akhir = $tabungan->saldo + $validatedData['jumlah_stor'];
        $transaksi->tipe_transaksi = 'Stor';
        $transaksi->pembayaran = 'Tunai';
        $transaksi->status = 'success';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id;
        $transaksi->tabungan_id = $tabungan->id;

        // Update tabungan
        $tabungan->saldo = $transaksi->saldo_akhir;
        $tabungan->premi = $transaksi->saldo_akhir * 0.025;
        $tabungan->sisa = $tabungan->saldo - $tabungan->premi;

        // Kirim WhatsApp
        try {
            // Normalisasi nomor: strip non-digit, format E.164
            $digits = preg_replace('/\D+/', '', $user->kontak);
            if (Str::startsWith($digits, '0')) {
                $digits = '62'.substr($digits, 1);
            } elseif (Str::startsWith($digits, '8')) {
                $digits = '62'.$digits;
            }

            // Format angka
            $fmt  = fn($n) => number_format($n, 0, ',', '.');
            $msg  = "Halo, {$user->name} 👋\n\n";
            $msg .= "*Stor Tabungan* Anda Berhasil:\n\n";
            $msg .= "🔹 Saldo Awal : Rp " . $fmt($transaksi->saldo_awal) . "\n";
            $msg .= "🔹 *Jumlah Stor : Rp " . $fmt($transaksi->jumlah_transaksi) . "*\n";
            $msg .= "🔹 Saldo Akhir : Rp " . $fmt($transaksi->saldo_akhir) . "\n\n";
            $msg .= "🙏 Terima kasih.\n";
            $msg .= "_Cek detail: sukarame-tabungan-siswa.my.id_\n";
            $msg .= "⚠️ _Pesan otomatis. Jangan dibalas._";

            $token     = config('services.wablas.token');
            $secret    = config('services.wablas.secret_key');
            $endpoint  = config('services.wablas.endpoint');
            $payload   = ['data' => [[
                'phone'   => $digits,
                'message' => $msg,
                'isGroup' => 'false',
            ]]];

            $response = Http::retry(3, 100)
                ->withHeaders([
                    'Authorization' => "{$token}.{$secret}",
                    'Content-Type'  => 'application/json',
                ])
                ->post($endpoint, $payload);

            if (! $response->ok() || ! data_get($response->json(), 'status')) {

                Log::error('WA gagal: '. $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error kirim WA: '. $e->getMessage());
        }

        $transaksi->save();
        $tabungan->save();

        return redirect()->back()->with([
            'success' => 'Tabungan berhasil disimpan',
            'alert-type' => 'success',
            'alert-message' => 'Tabungan berhasil disimpan',
            'alert-duration' => 3000,
        ]);
    }

    /**
    * Menyimpan stor tabungan siswa secara masal / perkelas oleh Walikelas.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */

    public function walikelas_storMasalTabungan(Request $request)
    {
        $items = $request->input('input', []);

        foreach ($items as $index => $data) {
            $stor = str_replace([',', '.'], '', $data['stor']);
            if (! is_numeric($stor) || (int)$stor < 1000) {
                continue;
            }

            $jumlahStor = (int)$stor;
            $user = User::where('username', $data['username'])->first();
            if (! $user || ! $user->tabungan) {
                continue;
            }

            $saldoAwal  = $user->tabungan->saldo;
            $saldoAkhir = $saldoAwal + $jumlahStor;

            $tabungan = $user->tabungan;
            $tabungan->saldo = $saldoAkhir;
            $tabungan->premi = $saldoAkhir * 0.025;
            $tabungan->sisa  = $saldoAkhir - $tabungan->premi;
            $tabungan->save();

            $transaksi = Transaksi::create([
                'user_id'            => $user->id,
                'tabungan_id'        => $tabungan->id,
                'jumlah_transaksi'   => $jumlahStor,
                'saldo_awal'         => $saldoAwal,
                'saldo_akhir'        => $saldoAkhir,
                'tipe_transaksi'     => 'Stor',
                'pembayaran'         => 'Tunai',
                'status'             => 'success',
                'pembuat'            => auth()->user()->name,
                'token_stor'         => Str::random(10),
            ]);

            // Kirim WhatsApp langsung di dalam fungsi dengan delay 15 detik per orang
            try {
                $digits = preg_replace('/\D+/', '', $user->kontak);
                if (Str::startsWith($digits, '0')) {
                    $digits = '62'.substr($digits, 1);
                } elseif (Str::startsWith($digits, '8')) {
                    $digits = '62'.$digits;
                }

                $fmt = fn($n) => number_format($n, 0, ',', '.');
                $msg = "Halo, {$user->name} 👋\n\n";
                $msg .= "*Stor Tabungan* Anda Berhasil:\n\n";
                $msg .= "🔹 Saldo Awal : Rp " . $fmt($transaksi->saldo_awal) . "\n";
                $msg .= "🔹 *Jumlah Stor : Rp " . $fmt($transaksi->jumlah_transaksi) . "*\n";
                $msg .= "🔹 Saldo Akhir : Rp " . $fmt($transaksi->saldo_akhir) . "\n\n";
                $msg .= "🙏 Terima kasih.\n";
                $msg .= "_Cek detail: sukarame-tabungan-siswa.my.id_\n";
                $msg .= "⚠️ _Pesan otomatis. Jangan dibalas._";

                $token    = config('services.wablas.token');
                $secret   = config('services.wablas.secret_key');
                $endpoint = config('services.wablas.endpoint');
                $payload  = ['data' => [[
                    'phone'   => $digits,
                    'message' => $msg,
                    'isGroup' => 'false',
                ]]];

                $response = Http::withHeaders([
                    'Authorization' => "{$token}.{$secret}",
                    'Content-Type'  => 'application/json',
                ])->post($endpoint, $payload);

                if (! $response->ok() || ! data_get($response->json(), 'status')) {
                    Log::error('WA gagal: ' . $response->body());
                }

                sleep(15); // delay 15 detik antar pesan sesuai limit paket

            } catch (\Exception $e) {
                Log::error('Error kirim WA: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Data stor tabungan berhasil disimpan.')->with('alert-type', 'success')->with('alert-message', 'Data stor tabungan berhasil disimpan.')->with('alert-duration', 3000);
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

        // Ambil saldo dan terbilang
        $nominal = $user->tabungan->saldo;
        $terbilang = RupiahHelper::terbilangRupiah($nominal);

        $invoice = null;

        // Cari transaksi terbaru user yang berstatus 'pending'
        $pendingTransaksi = Transaksi::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingTransaksi && $pendingTransaksi->external_id) {
            // Ambil data invoice dari Xendit
            $response = Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
                ->get('https://api.xendit.co/v2/invoices', [
                    'external_id' => $pendingTransaksi->external_id,
                ]);

            if ($response->successful() && count($response->json()) > 0) {
                $invoice = $response->json()[0];

                $invoice['expiry_date_carbon'] = Carbon::parse($invoice['expiry_date']);
            }
        }

        $nominal = auth()->user()->tabungan->saldo ;
        $terbilang = RupiahHelper::terbilangRupiah($nominal);
        return view('siswa.tabungan.stor', compact('nominal', 'terbilang', 'invoice'));
    }

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
        $jumlahStor = $request->jumlah_stor;

        Configuration::setXenditKey(config('xendit.api_key'));
        $apiInstance = new InvoiceApi();

        $externalId = 'stor-' . $user->id . '-' . time();
        $successRedirectUrl = route('payment.success');

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
            $transaksi->token_stor = \Illuminate\Support\Str::random(10);
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

                // Kirim email (dibungkus try-catch sendiri)
                // try {
                //     Mail::to($user->email)->send(new TabunganStoredMail($user, $transaksi));
                // } catch (\Exception $e) {
                //     \Log::error('Gagal mengirim email stor tabungan: ' . $e->getMessage());
                // }

                // Kirim pesan WhatsApp (dibungkus try-catch sendiri)
                try {
                    $token = '9mF7bUeEeQ84gN21aWNF';

                    $nomor = preg_replace('/[^0-9]/', '', $user->kontak);

                    if (substr($nomor, 0, 2) === '62') {
                        $nomor = '0' . substr($nomor, 2);
                    }
                    if (substr($nomor, 0, 1) === '0') {
                        $nomor = substr($nomor, 1);
                    }

                    $saldoAwal = number_format($transaksi->saldo_awal, 0, ',', '.');
                    $jumlahStor = number_format($transaksi->jumlah_transaksi, 0, ',', '.');
                    $saldoAkhir = number_format($transaksi->saldo_akhir, 0, ',', '.');

                    $pesan = "Halo,\n {$user->name} 👋\n\n" .
                        "*Stor Tabungan* Anda Berhasil :\n\n" .
                        "🔹 Saldo Awal : Rp {$saldoAwal}\n" .
                        "🔹 *Jumlah Stor : Rp {$jumlahStor}*\n" .
                        "🔹 Saldo Akhir : Rp {$saldoAkhir}\n\n" .
                        "Terima kasih telah mempercayakan tabungan Anda kepada kami. 🙏\n\n" .
                        "_Hubungi Bendahara jika ada pertanyaan. Notifikasi ini dibatasi 1000/bulan. Jika tidak menerima pesan saat stor/tarik, kunjungi sukarame-tabungan-siswa.my.id._";

                    Http::withHeaders([
                        'Authorization' => $token,
                    ])->asForm()->post('https://api.fonnte.com/send', [
                        'target' => $nomor,
                        'message' => $pesan,
                        'countryCode' => '62',
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim WhatsApp: ' . $e->getMessage());
                }

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


    public function success()
    {
        return view('payment.success');
    }
}
