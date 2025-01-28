<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Tabungan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Helpers\RupiahHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class TabunganController extends Controller
{
    // Bendahara ------------------------------------------------------------------------------------------------------------------------------------------------
    public function bendahara_index()
    {
        $kelas1 = Transaksi::whereHas('user.kelas', function ($query) {
            $query->where('kelas_id', 1);
        })->paginate(10);

        $kelas2 = Transaksi::whereHas('user.kelas', function ($query) {
            $query->where('kelas_id', 2);
        })->paginate(10);

        $kelas3 = Transaksi::whereHas('user.kelas', function ($query) {
            $query->where('kelas_id', 3);
        })->paginate(10);

        $kelas4 = Transaksi::whereHas('user.kelas', function ($query) {
            $query->where('kelas_id', 4);
        })->paginate(10);

        $kelas5 = Transaksi::whereHas('user.kelas', function ($query) {
            $query->where('kelas_id', 5);
        })->paginate(10);

        $kelas6 = Transaksi::whereHas('user.kelas', function ($query) {
            $query->where('kelas_id', 6);
        })->paginate(10);

        $transaksi_masuk = Transaksi::where('tipe_transaksi', 'Stor')->whereDate('created_at', Carbon::today())->sum('jumlah_transaksi');
        $transaksi_keluar = Transaksi::where('tipe_transaksi', 'Tarik')->whereDate('created_at', Carbon::today())->sum('jumlah_transaksi');
        $jumlah_saldo = Tabungan::whereDate('updated_at', Carbon::today())->sum('saldo');

        return view('bendahara.tabungan.index', compact('transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo', 'kelas1','kelas2','kelas3','kelas4','kelas5','kelas6'));
    }

    public function bendahara_stor()
    {
        return view('bendahara.tabungan.stor');
    }

    public function bendahara_storMasal($id)
    {
        $kelas = Kelas::find($id);
        $siswa = User::where('kelas_id', $id)->get();
        $walikelas = User::where('kelas_id', $id)->where('roles_id', 3)->first();
        $kemarin = Carbon::yesterday();

        $kemarin = Carbon::yesterday()->toDateString();

        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->whereHas('user.kelas', function ($query) {
                $query->where('kelas_id', $kelas);
            })
            ->sum('jumlah_transaksi');

        return view('bendahara.tabungan.stor_masal', compact('kelas', 'siswa', 'walikelas', 'jumlahTransaksiKemarin'));
    }

    public function bendahara_tarik()
    {
        return view('bendahara.tabungan.tarik');
    }

    public function bendahara_search(Request $request)
    {
        $username = $request->get('username');

        $user = DB::table('users')
            ->join('tabungans', 'tabungans.user_id', '=', 'users.id')
            ->join('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->where('users.username', $username)
            ->select('users.name as user_name', 'kelas.name as kelas_name', 'tabungans.saldo as tabungan_saldo')
            ->first();

        if ($user) {
            return response()->json([
                'name' => $user->user_name,
                'kelas' => $user->kelas_name,
                'tabungan' => $user->tabungan_saldo,
            ]);
        } else {
            return response()->json([
                'name' => 'Tidak Ada',
                'kelas' => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
            ]);
        }
    }

    public function bendahara_storTabungan(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_tabungan' => 'required|numeric|min:0',
            'jumlah_stor' => 'required|numeric|min:0',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric' => 'Jumlah tabungan harus berupa angka.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric' => 'Jumlah stor harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_stor'];
        $transaksi->saldo_awal = $validatedData['jumlah_tabungan'];
        $transaksi->saldo_akhir = $validatedData['jumlah_tabungan'] + $validatedData['jumlah_stor'] ;
        $transaksi->tipe_transaksi = 'Stor';
        $transaksi->pembayaran = 'Offline';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id ;
        $transaksi->tabungan_id = $user->tabungan->id;

        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $tabungan->saldo = $transaksi->saldo_akhir ;
        $tabungan->premi = $transaksi->saldo_akhir / 100 * 2.5 ;
        $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi ;

        $transaksi->save();

        $tabungan->save();

        return redirect()->back()->with('success', 'Tabungan berhasil disimpan')->with('alert-type', 'success')->with('alert-message', 'Tabungan berhasil disimpan')->with('alert-duration', 3000);
    }

    public function bendahara_storMasalTabungan(Request $request)
    {
        foreach ($request->input('input') as $data) {
            if (empty($data['stor']) || $data['stor'] <= 0) {
                continue;
            }

            $data['stor'] = str_replace([',', '.'], '', $data['stor']);

            $user = User::where('username', $data['username'])->first();

            if (!$user) {
                continue;
            }

            $transaksi = new Transaksi();
            $transaksi->jumlah_transaksi = $data['stor'];
            $transaksi->saldo_awal = $data['saldo'] ?? 0;
            $transaksi->saldo_akhir = $transaksi->saldo_awal + $data['stor'];
            $transaksi->tipe_transaksi = 'Stor';
            $transaksi->pembayaran = 'Offline';
            $transaksi->pembuat = auth()->user()->name;
            $transaksi->token_stor = \Illuminate\Support\Str::random(10);
            $transaksi->user_id = $user->id;
            $transaksi->tabungan_id = $user->tabungan->id ?? null;

            $tabungan = Tabungan::where('user_id', $user->id)->first();
            if ($tabungan) {
                $tabungan->saldo = $transaksi->saldo_akhir;
                $tabungan->premi = $transaksi->saldo_akhir / 100 * 2.5;
                $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi;
                $tabungan->save();
            }

            $transaksi->save();
        }

        return redirect()->back()->with('success', 'Data stor tabungan berhasil disimpan.')->with('alert-type', 'success')->with('alert-message', 'Data stor tabungan berhasil disimpan.')->with('alert-duration', 3000);
    }

    public function bendahara_tarikTabungan(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_tabungan' => 'required|numeric|min:0',
            'jumlah_tarik' => 'required|numeric|min:0',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric' => 'Jumlah tabungan harus berupa angka.',
            'jumlah_tarik.required' => 'Jumlah tarik harus diisi.',
            'jumlah_tarik.numeric' => 'Jumlah tarik harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();

        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        // Cek apakah jumlah tarik melebihi saldo awal
        if ($validatedData['jumlah_tarik'] > $tabungan->saldo) {
            return redirect()->back()->withErrors(['jumlah_tarik' => 'Penarikan tabungan melebihi saldo tabungan'])->withInput();
        }

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_tarik'];
        $transaksi->saldo_awal = $tabungan->saldo;
        $transaksi->saldo_akhir = $tabungan->saldo - $validatedData['jumlah_tarik'];
        $transaksi->tipe_transaksi = 'Tarik';
        $transaksi->pembayaran = 'Offline';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id;
        $transaksi->tabungan_id = $tabungan->id;

        $tabungan->saldo = $transaksi->saldo_akhir;
        $tabungan->premi = $tabungan->saldo / 100 * 2.5;
        $tabungan->sisa = $tabungan->saldo - $tabungan->premi;

        $transaksi->save();
        $tabungan->save();

        return redirect()->back()->with('success', 'Tabungan berhasil ditarik')->with('alert-type', 'success')->with('alert-message', 'Tabungan berhasil ditarik')->with('alert-duration', 3000);
    }


    // Walikelas ------------------------------------------------------------------------------------------------------------------------------------------------
    public function walikelas_index()
    {
        $kelas_id = Auth::user()->kelas_id;

        $transaksi_masuk = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Stor')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $transaksi_keluar = Transaksi::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->where('tipe_transaksi', 'Tarik')
        ->whereDate('created_at', Carbon::today())
        ->sum('jumlah_transaksi');

        $jumlah_saldo = Tabungan::whereHas('user', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->whereDate('updated_at', Carbon::today())
        ->sum('saldo');

        $kelas = Transaksi::whereHas('user.kelas', function ($query) use ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        })->paginate(10);


        return view('walikelas.tabungan.index', compact('transaksi_masuk', 'transaksi_keluar', 'jumlah_saldo', 'kelas'));
    }

    public function walikelas_stor()
    {
        return view('walikelas.tabungan.stor');
    }

    public function walikelas_storMasal()
    {
        $kelas = auth()->user()->kelas;
        $siswa = User::where('kelas_id', $kelas->id)->get();
        $walikelas = User::where('kelas_id', $kelas->id)->where('roles_id', 3)->first();
        $kemarin = Carbon::yesterday()->toDateString();

        $jumlahTransaksiKemarin = Transaksi::whereDate('created_at', $kemarin)
            ->whereHas('user', function ($query) use ($kelas) {
                $query->where('kelas_id', $kelas->id);
            })
            ->sum('jumlah_transaksi');

        return view('walikelas.tabungan.stor_masal', compact('kelas', 'siswa', 'walikelas', 'jumlahTransaksiKemarin'));
    }

    public function walikelas_search(Request $request)
    {
        $username = $request->get('username');

        $user = DB::table('users')
            ->join('tabungans', 'tabungans.user_id', '=', 'users.id')
            ->join('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->where('users.username', $username)
            ->select('users.name as user_name', 'kelas.name as kelas_name', 'tabungans.saldo as tabungan_saldo')
            ->first();

        if ($user) {
            return response()->json([
                'name' => $user->user_name,
                'kelas' => $user->kelas_name,
                'tabungan' => $user->tabungan_saldo,
            ]);
        } else {
            return response()->json([
                'name' => 'Tidak Ada',
                'kelas' => 'Tidak Ada',
                'tabungan' => 'Tidak Ada',
            ]);
        }
    }

    public function walikelas_storTabungan(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'kelas' => 'required',
            'jumlah_tabungan' => 'required|numeric|min:0',
            'jumlah_stor' => 'required|numeric|min:0',
        ], [
            'username.required' => 'Id Tabungan harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'kelas.required' => 'Kelas harus diisi.',
            'jumlah_tabungan.required' => 'Jumlah tabungan harus diisi.',
            'jumlah_tabungan.numeric' => 'Jumlah tabungan harus berupa angka.',
            'jumlah_stor.required' => 'Jumlah stor harus diisi.',
            'jumlah_stor.numeric' => 'Jumlah stor harus berupa angka.',
        ]);

        $user = User::where('username', $validatedData['username'])->firstOrFail();

        $transaksi = new Transaksi();
        $transaksi->jumlah_transaksi = $validatedData['jumlah_stor'];
        $transaksi->saldo_awal = $validatedData['jumlah_tabungan'];
        $transaksi->saldo_akhir = $validatedData['jumlah_tabungan'] + $validatedData['jumlah_stor'] ;
        $transaksi->tipe_transaksi = 'Stor';
        $transaksi->pembayaran = 'Offline';
        $transaksi->pembuat = auth()->user()->name;
        $transaksi->token_stor = \Illuminate\Support\Str::random(10);
        $transaksi->user_id = $user->id ;
        $transaksi->tabungan_id = $user->tabungan->id;

        $tabungan = Tabungan::where('user_id', $user->id)->firstOrFail();

        $tabungan->saldo = $transaksi->saldo_akhir ;
        $tabungan->premi = $transaksi->saldo_akhir / 100 * 2.5 ;
        $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi ;

        $transaksi->save();

        $tabungan->save();

        return redirect()->back()->with('success', 'Tabungan berhasil disimpan')->with('alert-type', 'success')->with('alert-message', 'Tabungan berhasil disimpan')->with('alert-duration', 3000);
    }

    public function walikelas_storMasalTabungan(Request $request)
    {
        foreach ($request->input('input') as $data) {
            if (empty($data['stor']) || $data['stor'] <= 0) {
                continue;
            }

            $data['stor'] = str_replace([',', '.'], '', $data['stor']);

            $user = User::where('username', $data['username'])->first();

            if (!$user) {
                continue;
            }

            $transaksi = new Transaksi();
            $transaksi->jumlah_transaksi = $data['stor'];
            $transaksi->saldo_awal = $data['saldo'] ?? 0;
            $transaksi->saldo_akhir = $transaksi->saldo_awal + $data['stor'];
            $transaksi->tipe_transaksi = 'Stor';
            $transaksi->pembayaran = 'Offline';
            $transaksi->pembuat = auth()->user()->name;
            $transaksi->token_stor = \Illuminate\Support\Str::random(10);
            $transaksi->user_id = $user->id;
            $transaksi->tabungan_id = $user->tabungan->id ?? null;

            $tabungan = Tabungan::where('user_id', $user->id)->first();
            if ($tabungan) {
                $tabungan->saldo = $transaksi->saldo_akhir;
                $tabungan->premi = $transaksi->saldo_akhir / 100 * 2.5;
                $tabungan->sisa = $transaksi->saldo_akhir - $tabungan->premi;
                $tabungan->save();
            }

            $transaksi->save();
        }

        return redirect()->back()->with('success', 'Data stor tabungan berhasil disimpan.')->with('alert-type', 'success')->with('alert-message', 'Data stor tabungan berhasil disimpan.')->with('alert-duration', 3000);
    }

    // Siswa ------------------------------------------------------------------------------------------------------------------------------------------------
    public function siswa_stor()
    {
        $nominal = auth()->user()->tabungan->saldo ;
        $terbilang = RupiahHelper::terbilangRupiah($nominal);
        return view('siswa.tabungan.stor', compact('nominal', 'terbilang'));
    }

    public function siswa_tarik()
    {
        $nominal = auth()->user()->tabungan->saldo ;
        $terbilang = RupiahHelper::terbilangRupiah($nominal);
        return view('siswa.tabungan.tarik', compact('nominal', 'terbilang'));
    }
}
