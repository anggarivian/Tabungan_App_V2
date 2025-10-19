<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Rekap Tabungan — SDN Sukarame</title>
  <style>
    :root{--accent:#0b5ed7;--muted:#6c757d;--card-bg:#f8f9fa}
    body{font-family:DejaVu Sans, sans-serif;color:#222;margin:0;padding:12px;background:white}
    .sheet{max-width:960px;margin:0 auto;padding:16px;background:#fff}
    header{display:flex;justify-content:space-between;align-items:flex-start;gap:12px}
    h1{font-size:17px;margin:0}
    h2{font-size:13px;margin:6px 0 8px 0}
    .meta{font-size:11px;color:var(--muted)}
    hr{border:none;border-top:1px solid #e9ecef;margin:8px 0}
    .grid {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .grid .card {
      flex: 1 1 calc(25% - 8px);
      box-sizing: border-box;
    }
    .card{background:var(--card-bg);padding:6px 8px;border-radius:6px;box-shadow:0 1px 0 rgba(0,0,0,0.03)}
    .k{font-size:10px;color:var(--muted)}
    .v{font-weight:700;font-size:13px}
    table{width:100%;border-collapse:collapse;font-size:11px}
    th,td{padding:4px 6px;border:1px solid #e9ecef;text-align:left}
    th{background:#f1f3f5;font-weight:600}
    .right{text-align:right}
    .small{font-size:10px;color:var(--muted)}
    .footer{margin-top:12px;display:flex;justify-content:space-between;align-items:flex-end;gap:16px}
  </style>
</head>
<body>
  <div class="sheet">
    <header>
      <div>
        <h1>REKAP TABUNGAN — SDN SUKARAME</h1>
        <div class="meta">
          Periode: <strong>{{ $periodeStart }} — {{ $periodeEnd }}</strong>
          &nbsp; | &nbsp;
          Dibuat oleh: <strong>{{ $pembuat }}</strong>
        </div>
      </div>
      <div style="text-align:right">
        <div class="meta">Dicetak: {{ now()->translatedFormat('d F Y') }}</div>
      </div>
    </header>

    <hr />

    <h2>1. Ringkasan Eksekutif</h2>
    <table style="width:100%; margin-bottom:8px; border-collapse:separate; border-spacing:8px 4px;">
      <tr>
        <td style="background:#f8f9fa; padding:6px 8px; border-radius:6px; width:25%;">
          <div class="k">Total siswa tercatat</div>
          <div class="v">{{ $totalSiswa }}</div>
        </td>
        <td style="background:#f8f9fa; padding:6px 8px; border-radius:6px; width:25%;">
          <div class="k">Total saldo keseluruhan</div>
          <div class="v">Rp {{ number_format($totalSaldo,0,',','.') }}</div>
        </td>
        <td style="background:#f8f9fa; padding:6px 8px; border-radius:6px; width:25%;">
          <div class="k">Total Masuk</div>
          <div class="v">Rp {{ number_format($totalMasuk,0,',','.') }}</div>
        </td>
        <td style="background:#f8f9fa; padding:6px 8px; border-radius:6px; width:25%;">
          <div class="k">Total Keluar</div>
          <div class="v">Rp {{ number_format($totalKeluar,0,',','.') }}</div>
        </td>
      </tr>
    </table>

    <h2>2. Breakdown per Kelas</h2>
    <table style="margin-bottom:8px">
      <thead>
        <tr>
          <th>Kelas</th>
          <th class="right">Jumlah Siswa</th>
          <th class="right">Total Saldo (Rp)</th>
          <th class="right">Rata-rata/Siswa (Rp)</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kelasData as $row)
          <tr>
            <td>{{ $row['kelas'] }}</td>
            <td class="right">{{ $row['jumlah_siswa'] }}</td>
            <td class="right">{{ number_format($row['total_saldo'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($row['rata_rata'], 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <hr />

    <h2>3. Transaksi Masuk & Keluar per Bulan</h2>
    <table style="margin-bottom:8px;font-size:10px;width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left;border:1px solid #e9ecef;padding:4px 6px;">Bulan</th>
          <th style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">Masuk (Rp)</th>
          <th style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">Keluar (Rp)</th>
          <th style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">Net (Rp)</th>
        </tr>
      </thead>
      <tbody>
        @php
          $totalMasukBulan = 0;
          $totalKeluarBulan = 0;
          $totalNetBulan = 0;
        @endphp
        @foreach($transaksiPerBulan as $t)
          @php
            $totalMasukBulan += $t->total_masuk;
            $totalKeluarBulan += $t->total_keluar;
            $totalNetBulan += $t->net;
          @endphp
          <tr>
            <td style="border:1px solid #e9ecef;padding:4px 6px;">{{ $t->bulan_label }}</td>
            <td style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">{{ number_format($t->total_masuk, 0, ',', '.') }}</td>
            <td style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">{{ number_format($t->total_keluar, 0, ',', '.') }}</td>
            <td style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">{{ number_format($t->net, 0, ',', '.') }}</td>
          </tr>
        @endforeach
        <tr style="font-weight:700;background:#f1f3f5;">
          <td style="border:1px solid #e9ecef;padding:4px 6px;">TOTAL</td>
          <td style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">{{ number_format($totalMasukBulan, 0, ',', '.') }}</td>
          <td style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">{{ number_format($totalKeluarBulan, 0, ',', '.') }}</td>
          <td style="text-align:right;border:1px solid #e9ecef;padding:4px 6px;">{{ number_format($totalNetBulan, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    <hr />

    <h2>4. Detail Penting Lainnya</h2>

    <table style="width:100%;border-collapse:separate;border-spacing:8px;margin-top:10px;">
      @foreach(array_chunk($cards, 4) as $row)
      <tr>
        @foreach($row as $card)
        <td style="
            width:25%;
            background:#f8f9fa;
            border:1px solid #dee2e6;
            border-radius:8px;
            padding:10px 8px;
            vertical-align:middle;
        ">
          <div style="font-size:11px;font-weight:600;color:#6c757d;">
            {{ $card['title'] }}
          </div>
          <div style="font-size:14px;font-weight:700;color:#212529;margin-top:4px;">
            {{ $card['prefix'] ?? '' }}{{ $card['value'] }}{{ $card['suffix'] ?? '' }}
          </div>
        </td>
        @endforeach
      </tr>
      @endforeach
    </table>

    <table style="width:100%; margin-top:20px; font-size:10px; border-collapse:collapse;">
      <tr valign="top ">
        <!-- Catatan -->
        <td style="width:45%; vertical-align:top; border:none;">
          <strong>Catatan:</strong><br>
          <span style="color:#6c757d;">Dicetak otomatis oleh sistem — {{ now()->translatedFormat('H:i, d F Y') }}</span>
        </td>

        <!-- Signature area -->
        <td style="width:55%; border:none;">
          <table style="width:100%; text-align:center; font-size:11px; border-collapse:collapse;">
            <tr>
              <td style="border:none;">
                <div style="height:40px;"></div>
                <div style="font-weight:700;">Kepala Sekolah</div>
                <div style="font-size:10px; color:#6c757d;">SDN Sukarame</div>
              </td>
              <td style="border:none;">
                <div style="height:40px;"></div>
                <div style="font-weight:700;">Bendahara</div>
                <div style="font-size:10px; color:#6c757d;">SDN Sukarame</div>
              </td>
              <td style="border:none;">
                <div style="height:40px;"></div>
                <div style="font-weight:700;">Angga Saepul Rivian</div>
                <div style="font-size:10px; color:#6c757d;">Operator Sekolah</div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>