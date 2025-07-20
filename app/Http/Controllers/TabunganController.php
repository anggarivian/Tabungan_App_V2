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
        $perPage = request('perPage', 10);
        $kelasList = [];

        for ($i = 1; $i <= 7; $i++) {
            $transaksi = Transaksi::whereHas('user.kelas', fn($q) => $q->where('kelas_id', $i))
                ->whereDate('created_at', Carbon::today())
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $stor = Transaksi::whereHas('user.kelas', fn($q) => $q->where('kelas_id', $i))
                ->where('tipe_transaksi', 'Stor')
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->sum('jumlah_transaksi');

            $tarik = Transaksi::whereHas('user.kelas', fn($q) => $q->where('kelas_id', $i))
                ->where('tipe_transaksi', 'Tarik')
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->sum('jumlah_transaksi');

            $kelasList["kelas$i"] = [
                'data' => $transaksi,
                'stor' => $stor,
                'tarik'=> $tarik,
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

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->where('created_at', '>=', Carbon::today()->startOfDay())
            ->count();
        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
            ->where('created_at', '>=', Carbon::today()->startOfDay())
            ->count();

        return view('bendahara.tabungan.index', compact('transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo_tunai', 'jumlah_saldo_digital', 'kelasList', 'storKali', 'tarikKali'));
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

      // Tampilkan form stor masal
    public function bendahara_storMasal($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Urutkan supaya tidak acak
        $siswa = User::where('kelas_id', $id)
                    ->where('roles_id', 4)
                    ->with('tabungan')
                    ->orderBy('username')
                    ->get();

        $walikelas = User::where('kelas_id', $id)
                        ->where('roles_id', 3)
                        ->first();

        $kemarin = Carbon::yesterday()->toDateString();
        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->whereHas('user', fn($q) => $q->where('kelas_id', $id))
            ->where('status', 'success')
            ->sum('jumlah_transaksi');

        return view('bendahara.tabungan.stor_masal', compact(
            'kelas', 'siswa', 'walikelas', 'jumlahTransaksiKemarin'
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
            'jumlah_stor' => str_replace('.', '', $request->jumlah_stor),
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

        $user = User::where('username', $validated['username'])->firstOrFail();
        $tabungan = $user->tabungan;

        $awal = $tabungan->saldo;
        $stor = $validated['jumlah_stor'];
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
        ]);
        $transaksi->user()->associate($user);
        $transaksi->tabungan()->associate($tabungan);
        $transaksi->save();

        $tabungan->update([
            'saldo' => $akhir,
            'premi' => $premi,
            'sisa'  => $sisa,
        ]);

        SendWhatsAppMessage::dispatch($transaksi);

        return redirect()->back()
            ->with('success', 'Data stor tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data stor tabungan berhasil disimpan.')
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
        $sanitized = collect($request->input('input', []))
            ->map(fn($row) => [
                'username' => $row['username'] ?? null,
                'stor'     => preg_replace('/\D+/', '', $row['stor'] ?? ''), // fix di sini
            ])
            ->filter(fn($row) => $row['username'] && is_numeric($row['stor']) && (int)$row['stor'] >= 1000)
            ->values()
            ->toArray();

        // Log untuk debug
        \Log::info('Sanitized input:', $sanitized);

        if (count($sanitized) === 0) {
            return redirect()->back()->withErrors(['Data tidak valid atau kosong.']);
        }

        $request->merge(['input' => $sanitized]);

        $request->validate([
            'input.*.username' => 'required|string|exists:users,username',
            'input.*.stor'     => 'required|numeric|min:1000',
        ], [
            'input.*.stor.min' => 'Minimal stor Rp 1.000',
        ]);

        DB::transaction(function() use ($sanitized) {
            foreach ($sanitized as $row) {
                $user = User::where('username', $row['username'])->first();
                if (! $user || ! $user->tabungan) continue;

                $tabungan = $user->tabungan()->lockForUpdate()->first();
                $stor     = (int)$row['stor'];
                $awal     = $tabungan->saldo;
                $akhir    = $awal + $stor;

                // Hitung premi & sisa
                $premi = $akhir * 0.025;
                $sisa  = $akhir - $premi;

                $tabungan->update([
                    'saldo' => $akhir,
                    'premi' => $premi,
                    'sisa'  => $sisa,
                ]);

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
                $transaksi->load('user');

                SendWhatsAppMessage::dispatch($transaksi);
            }
        });

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
            'jumlah_tabungan' => str_replace('.', '', $request->jumlah_tabungan),
            'jumlah_tarik'    => str_replace('.', '', $request->jumlah_tarik),
        ]);

        $validated = $request->validate([
            'username'         => 'required|exists:users,username',
            'name'             => 'required',
            'kelas'            => 'required',
            'jumlah_tabungan'  => 'required|numeric|min:0',
            'jumlah_tarik'     => 'required|numeric|min:1',
        ], [
            'username.required'         => 'Id Tabungan harus diisi.',
            'username.exists'           => 'Id Tabungan tidak ditemukan.',
            'name.required'             => 'Nama harus diisi.',
            'kelas.required'            => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric'  => 'Jumlah tabungan harus berupa angka.',
            'jumlah_tarik.required'    => 'Jumlah tarik harus diisi.',
            'jumlah_tarik.numeric'     => 'Jumlah tarik harus berupa angka.',
        ]);

        $user     = User::where('username', $validated['username'])->firstOrFail();
        $tabungan = $user->tabungan;
        $awal     = $tabungan->saldo;

        $tarik = $validated['jumlah_tarik'];
        $premi = ceil(($tarik * 0.05) / 1000) * 1000;

        $totalPenarikan = $tarik + $premi;

        if ($totalPenarikan > $awal) {
            return redirect()->back()
                ->withErrors(['jumlah_tarik' => 'Total penarikan (termasuk biaya admin) melebihi saldo tabungan'])
                ->withInput();
        }

        $akhir = $awal - $totalPenarikan;

        $transaksi = new Transaksi([
            'jumlah_transaksi' => $totalPenarikan,
            'saldo_awal'       => $awal,
            'saldo_akhir'      => $akhir,
            'tipe_transaksi'   => 'Tarik',
            'pembayaran'       => 'Tunai',
            'status'           => 'success',
            'pembuat'          => auth()->user()->name,
            'token_stor'       => Str::random(10),
        ]);
        $transaksi->user()->associate($user);
        $transaksi->tabungan()->associate($tabungan);
        $transaksi->save();

        $tabungan->update([
            'saldo' => $akhir,
            'premi' => $premi,
            'sisa'  => $akhir,
        ]);

        $bendahara = User::findOrFail(2)->tabungan;
        $bendahara->increment('saldo', $premi);

        SendWhatsAppMessage::dispatch($transaksi);

        return redirect()->back()
            ->with('success', 'Data tarik tabungan berhasil disimpan.')
            ->with('alert-type', 'success')
            ->with('alert-message', 'Data tarik tabungan berhasil disimpan.')
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

        $storKali = Transaksi::where('tipe_transaksi', 'Stor')
            ->where('status', 'success')
            ->where('created_at', '>=', Carbon::today()->startOfDay())
            ->whereHas('user', function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id);
            })
            ->count();

        $tarikKali = Transaksi::where('tipe_transaksi', 'Tarik')
            ->where('status', 'success')
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
