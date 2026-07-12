<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Rekap Tabungan — SDN Sukarame</title>
  <style>
    @page { margin: 25px 35px; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #222; font-size: 10px; line-height: 1.3; margin: 0; padding: 0; }
    
    header { border-bottom: 1px solid #e9ecef; padding-bottom: 8px; margin-bottom: 8px; }
    h1 { font-size: 16px; margin: 0; }
    h2 { font-size: 12px; margin: 8px 0 6px 0; }
    .meta { font-size: 10px; color: #6c757d; }
    
    /* Layout Cards (Pengganti Grid) */
    .grid-table { width: 100%; border-collapse: separate; border-spacing: 5px; margin-left: -5px; margin-bottom: 5px; }
    .grid-table td { background: #f8f9fa; padding: 6px 8px; border-radius: 6px; width: 25%; border: 1px solid #f1f3f5; vertical-align: top; }
    .k { font-size: 9px; color: #6c757d; display: block; margin-bottom: 2px; }
    .v { font-weight: bold; font-size: 12px; }

    /* Normal Data Tables */
    .data-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 8px; }
    .data-table th, .data-table td { padding: 4px 6px; border: 1px solid #e9ecef; text-align: left; }
    .data-table th { background: #f1f3f5; font-weight: 600; }
    
    .right { text-align: right; }
    .center { text-align: center; }
    .small { font-size: 9px; color: #6c757d; }
  </style>
</head>
<body>

  <header>
    <table style="width: 100%; border: none;">
      <tr>
        <td style="border: none; padding: 0;">
          <h1>REKAP TABUNGAN — SDN SUKARAME</h1>
          <div class="meta">Periode: <strong>{{ $periode->nama }}</strong> &nbsp; | &nbsp; Dibuat oleh: <strong>{{ auth()->user()->name }}</strong></div>
        </td>
        <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
          <div class="meta">Dicetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
        </td>
      </tr>
    </table>
  </header>

  <h2>1. Ringkasan Eksekutif</h2>
  <table class="grid-table">
    <tr>
      <td><span class="k">Total siswa tercatat</span><span class="v">{{ number_format($totalSiswa, 0, ',', '.') }}</span></td>
      <td><span class="k">Total saldo keseluruhan</span><span class="v">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</span></td>
      <td><span class="k">Total Masuk</span><span class="v">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span></td>
      <td><span class="k">Total Keluar</span><span class="v">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span></td>
    </tr>
  </table>

  <h2>2. Breakdown per Kelas</h2>
  <table class="data-table">
    <thead>
      <tr>
        <th>Kelas</th>
        <th class="right">Jumlah Siswa</th>
        <th class="right">Total Saldo (Rp)</th>
        <th class="right">Rata-rata/Siswa (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rekapRombel as $rombel)
      <tr>
        <td>{{ $rombel->name }}</td>
        <td class="right">{{ $rombel->users_count }}</td>
        <td class="right">{{ number_format($rombel->total_saldo, 0, ',', '.') }}</td>
        <td class="right">{{ $rombel->users_count > 0 ? number_format($rombel->total_saldo / $rombel->users_count, 0, ',', '.') : 0 }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="center">Belum ada data rombel</td></tr>
      @endforelse
    </tbody>
  </table>

  <h2>3. Transaksi Masuk & Keluar per Bulan</h2>
  <table class="data-table">
    <thead>
      <tr>
        <th>Bulan</th>
        <th class="right">Masuk (Rp)</th>
        <th class="right">Keluar (Rp)</th>
        <th class="right">Net (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($transaksiBulanan as $tb)
      <tr>
        <td>{{ \Carbon\Carbon::create()->month($tb->bulan)->translatedFormat('F') }} {{ $tb->tahun }}</td>
        <td class="right">{{ number_format($tb->masuk, 0, ',', '.') }}</td>
        <td class="right">{{ number_format($tb->keluar, 0, ',', '.') }}</td>
        <td class="right">{{ number_format($tb->masuk - $tb->keluar, 0, ',', '.') }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="center">Belum ada transaksi</td></tr>
      @endforelse
      <tr style="background: #f1f3f5; font-weight: bold;">
        <td>TOTAL</td>
        <td class="right">{{ number_format($totalMasukBulan, 0, ',', '.') }}</td>
        <td class="right">{{ number_format($totalKeluarBulan, 0, ',', '.') }}</td>
        <td class="right">{{ number_format($totalMasukBulan - $totalKeluarBulan, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  <h2>4. Detail Penting Lainnya</h2>
  <table class="grid-table">
    <tr>
      <td><span class="k">Siswa dengan saldo 0</span><span class="v">{{ $siswaSaldoNol }} siswa</span></td>
      <td><span class="k">Siswa aktif menabung</span><span class="v">{{ $siswaAktifMenabung }} siswa</span></td>
      <td><span class="k">Saldo tertinggi</span><span class="v">Rp {{ number_format($saldoTertinggi, 0, ',', '.') }}</span></td>
      <td><span class="k">Saldo terendah</span><span class="v">Rp {{ number_format($saldoTerendah, 0, ',', '.') }}</span></td>
    </tr>
    <tr>
      <td><span class="k">Rata-rata transaksi/bln</span><span class="v">{{ $rataTransaksiBulan }} kali</span></td>
      <!-- <td><span class="k">Tingkat retensi saldo</span><span class="v">+{{ $tingkatPertumbuhan }}%</span></td> -->
       <td><span class="k">Kelas terdepan</span><span class="v">{{ $kelasTerdepan }}</span></td>
      <!-- <td><span class="k">Tingkat penarikan</span><span class="v">{{ $tingkatPenarikan }}%</span></td> -->
      <td><span class="k">Tahun akademik</span><span class="v">{{ $periode->nama }}</span></td>
      <td><span class="k">Saldo Xendit</span><span class="v">Rp {{ number_format($saldoXendit, 0, ',', '.') }}</span></td>
    </tr>
    <tr>
      <!-- <td><span class="k">Rata-rata transaksi/bln</span><span class="v">{{ $rataTransaksiBulan }} kali</span></td> -->
      <!-- <td><span class="k">Bulan puncak</span><span class="v">{{ $bulanPuncak }}</span></td> -->
      <!-- <td><span class="k">Kelas terdepan</span><span class="v">{{ $kelasTerdepan }}</span></td> -->
      <!-- <td><span class="k">Status keseluruhan laporan</span><span class="v">Sesuai / Tidak Sesuai</span></td> -->
    </tr>
  </table>

  <table style="width: 100%; border: none; margin-top: 25px;">
    <tr>
      <td style="border: none; width: 60%;">
        <div class="small">
          <strong>Catatan:</strong><br>
          Laporan digenerate secara otomatis oleh sistem SakuRame.<br>
          Validitas laporan ini berdasar data real-time pada saat pencetakan.
        </div>
      </td>
      <td style="border: none; width: 40%; text-align: center;">
        <div style="font-weight: bold;">Sukarame, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
      </td>
    </tr>
  </table>

  <table style="width: 100%; border: none; margin-top: 25px;">
    <tr>
      <td style="border: none; width: 33%; text-align: center;">
        <div style="font-weight: bold; font-size: 10px;">Kepala Sekolah</div>
        <div class="small">SDN Sukarame</div>
        <br><br><br><br>
        <div style="font-weight: bold;"><u>_____________________________</u></div>
        <div class="small">NIP: ..............................</div>
      </td>
      <td style="border: none; width: 34%; text-align: center;">
        <div style="font-weight: bold; font-size: 10px;">Bendahara</div>
        <div class="small">SDN Sukarame</div>
        <br><br><br><br>
        <div style="font-weight: bold;"><u>_____________________________</u></div>
        <div class="small">NIP: ..............................</div>
      </td>
      <td style="border: none; width: 33%; text-align: center;">
        <div style="font-weight: bold; font-size: 10px;">{{ auth()->user()->name }}</div>
        <div class="small">Operator Sekolah</div>
        <br><br><br><br>
        <div style="font-weight: bold;"><u>_____________________________</u></div>
        <div class="small">NIP: ..............................</div>
      </td>
    </tr>
  </table>

</body>
</html>