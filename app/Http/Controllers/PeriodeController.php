<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Periode;
use App\Models\Tabungan;
use App\Helpers\PeriodeHepler;
use Illuminate\Http\Request;
use Xendit\BalanceAndTransaction\BalanceApi;

class PeriodeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        $periodes = Periode::latest()
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view(
            'bendahara.periode.index',
            compact('periodes')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'=>'required|unique:periodes',
            'tanggal_mulai'=>'required',
            'tanggal_selesai'=>'required'
        ]);

        $periode = Periode::create([
            'nama'=>$request->nama,
            'tanggal_mulai'=>$request->tanggal_mulai,
            'tanggal_selesai'=>$request->tanggal_selesai,
            'status'=>0
        ]);

        $siswas = User::where('roles_id',4)->get();

        foreach($siswas as $siswa){

            Tabungan::create([
                'user_id'=>$siswa->id,
                'periode_id'=>$periode->id,
                'saldo'=>0,
                'premi'=>0,
                'sisa'=>0
            ]);

        }

        return back()
        ->with('success','Periode berhasil dibuat.');
    }

    public function update(Request $request, Periode $periode)
    {
        $request->validate([
            'nama' => 'required|unique:periodes,nama,' . $periode->id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $periode->update([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function aktifkan(Periode $periode)
    {
        Periode::query()->update([
            'status'=>0
        ]);

        $periode->update([
            'status'=>1
        ]);

        return back()
        ->with('success','Periode berhasil diaktifkan.');
    }

    public function generateRekapPdf($id)
    {
        $periode = \App\Models\Periode::findOrFail($id);

        // 1. Ringkasan Eksekutif
        $totalSiswa = \App\Models\User::where('roles_id', 4)->where('status_siswa', 'aktif')->count();
        $totalSaldo = \App\Models\Tabungan::where('periode_id', $periode->id)->sum('saldo');
        
        $transaksi = \App\Models\Transaksi::where('periode_id', $periode->id)->where('status', 'success')->get();
        $totalMasuk = $transaksi->where('tipe_transaksi', 'Stor')->sum('jumlah_transaksi');
        $totalKeluar = $transaksi->where('tipe_transaksi', 'Tarik')->sum('jumlah_transaksi');

        // 2. Breakdown per Kelas
        $rekapRombel = \App\Models\Rombel::where('periode_id', $periode->id)
            ->withCount(['users' => function($q) { $q->where('roles_id', 4); }])
            ->get()
            ->map(function($rombel) use ($periode) {
                $rombel->total_saldo = \App\Models\Tabungan::where('periode_id', $periode->id)
                    ->whereHas('user.rombel', function($q) use ($rombel) {
                        $q->where('rombels.id', $rombel->id);
                    })->sum('saldo');
                return $rombel;
            })->sortByDesc('total_saldo'); // Urutkan dari saldo terbesar

        // 3. Transaksi Bulanan (SQL Raw untuk grup per bulan)
        $transaksiBulanan = \App\Models\Transaksi::selectRaw('
                YEAR(created_at) as tahun,
                MONTH(created_at) as bulan,
                SUM(CASE WHEN tipe_transaksi = "Stor" THEN jumlah_transaksi ELSE 0 END) as masuk,
                SUM(CASE WHEN tipe_transaksi = "Tarik" THEN jumlah_transaksi ELSE 0 END) as keluar
            ')
            ->where('periode_id', $periode->id)
            ->where('status', 'success')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        $totalMasukBulan = $transaksiBulanan->sum('masuk');
        $totalKeluarBulan = $transaksiBulanan->sum('keluar');

        // 4. Detail Penting Lainnya (Statistik Kompleks)
        $tabungan = \App\Models\Tabungan::where('periode_id', $periode->id)->get();
        
        // Cari siswa yang saldonya 0 atau belum punya tabungan sama sekali
        $siswaSaldoNol = $tabungan->where('saldo', '<=', 0)->count() + ($totalSiswa - $tabungan->count());
        $siswaAktifMenabung = $transaksi->where('tipe_transaksi', 'Stor')->pluck('user_id')->unique()->count();
        
        $saldoTertinggi = $tabungan->max('saldo') ?? 0;
        $saldoTerendah = $tabungan->where('saldo', '>', 0)->min('saldo') ?? 0;

        // Kalkulasi Persentase & Rata-rata
        $tingkatPenarikan = $totalMasuk > 0 ? round(($totalKeluar / $totalMasuk) * 100, 1) : 0;
        $tingkatPertumbuhan = $totalMasuk > 0 ? round((($totalMasuk - $totalKeluar) / $totalMasuk) * 100, 1) : 0;
        
        $jumlahBulan = $transaksiBulanan->count() > 0 ? $transaksiBulanan->count() : 1;
        $rataTransaksiBulan = round($transaksi->count() / $jumlahBulan);

        // Cari Bulan Puncak (Transaksi Terbanyak)
        $bulanPuncakData = $transaksiBulanan->sortByDesc(function($item) {
            return $item->masuk + $item->keluar;
        })->first();
        $bulanPuncak = $bulanPuncakData ? \Carbon\Carbon::create()->month($bulanPuncakData->bulan)->translatedFormat('F') . ' ' . $bulanPuncakData->tahun : '-';

        $kelasTerdepan = $rekapRombel->first()->name ?? '-';

        // 5. Data Tanda Tangan
        $kepsek = \App\Models\User::where('roles_id', 1)->first(); // Ambil data Kepsek
        $bendahara = \App\Models\User::where('roles_id', 2)->first() ?? auth()->user();

        $saldoXendit = 0;
        try {
            \Xendit\Configuration::setXenditKey(config('services.xendit.secret_key'));
            $balanceApi = new \Xendit\BalanceAndTransaction\BalanceApi();
            $balance = $balanceApi->getBalance('CASH');
            $saldoXendit = $balance['balance'] ?? 0;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memuat saldo Xendit: ' . $e->getMessage());
            $saldoXendit = null; // Set null sebagai penanda error/gangguan
        }

        // 6. Generate PDF menggunakan DOMPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bendahara.periode.rekap', compact(
            'periode', 'totalSiswa', 'totalSaldo', 'totalMasuk', 'totalKeluar',
            'rekapRombel', 'transaksiBulanan', 'totalMasukBulan', 'totalKeluarBulan',
            'siswaSaldoNol', 'siswaAktifMenabung', 'saldoTertinggi', 'saldoTerendah',
            'tingkatPenarikan', 'tingkatPertumbuhan', 'rataTransaksiBulan', 'bulanPuncak', 'kelasTerdepan',
            'kepsek', 'bendahara', 'saldoXendit'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Rekap_Periode_' . $periode->id . '.pdf');
    }
}