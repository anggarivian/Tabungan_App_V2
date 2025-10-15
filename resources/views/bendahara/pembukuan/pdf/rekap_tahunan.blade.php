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
    .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
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
    <div class="grid" style="margin-bottom:8px">
      <div class="card"><div class="k">Total siswa tercatat</div><div class="v">{{ $totalSiswa }}</div></div>
      <div class="card"><div class="k">Total saldo keseluruhan</div><div class="v">Rp {{ number_format($totalSaldo,0,',','.') }}</div></div>
      <div class="card"><div class="k">Total Masuk</div><div class="v">Rp {{ number_format($totalMasuk,0,',','.') }}</div></div>
      <div class="card"><div class="k">Total Keluar</div><div class="v">Rp {{ number_format($totalKeluar,0,',','.') }}</div></div>
    </div>

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

    <div class="footer">
      <div class="small">Dicetak otomatis oleh sistem — {{ now()->translatedFormat('H:i, d F Y') }}</div>
    </div>
  </div>
</body>
</html>