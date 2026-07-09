<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Tabungan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-size: 11px; font-family: Arial, sans-serif; color: #333; padding: 30px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { font-size: 14px; margin-bottom: 2px; }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 2px; }
        .title-report { font-size: 14px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        .meta-table { width: 100%; margin-bottom: 15px; border: none; }
        .meta-table td { border: none; padding: 3px 0; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .main-table th, .main-table td { border: 1px solid #333; padding: 7px 5px; text-align: left; }
        .main-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; }
        .footer-sign { width: 100%; margin-top: 30px; }
        .footer-sign td { border: none; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DINAS PENDIDIKAN PEMUDA DAN OLAHRAGA</h2>
        <h3>KABUPATEN CIANJUR</h3>
        <h1>SEKOLAH DASAR NEGERI SUKARAME</h1>
        <h4>KECAMATAN SUKANAGARA</h4>
    </div>

    <div class="title-report">Laporan Saldo Tabungan Siswa</div>

    @if($exportOption == 'siswa' && $user)
        <table class="meta-table">
            {{-- Menggunakan Null-safe (?->) bawaan PHP 8+ untuk cegah crash jika rombel kosong --}}
            <tr><td width="150">Kode Siswa (ID)</td><td width="10">:</td><td>{{ $user->rombel?->first()?->pivot?->kode ?? $user->rombels?->first()?->pivot?->kode ?? $user->username }}</td></tr>
            <tr><td>Nama Siswa</td><td>:</td><td>{{ $user->name }}</td></tr>
            <tr><td>Rombel</td><td>:</td><td>{{ $user->rombel?->first()?->name ?? $user->rombels?->first()?->name ?? '-' }}</td></tr>
            <tr><td>Total Saldo Utama</td><td>:</td><td>Rp {{ number_format($user->tabungan?->first()?->saldo ?? $user->tabungan?->saldo ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Limit Dapat Ditarik</td><td>:</td><td>Rp {{ number_format($user->tabungan?->first()?->sisa ?? $user->tabungan?->sisa ?? 0, 0, ',', '.') }}</td></tr>
        </table>
    @else
        <table class="meta-table">
            <tr><td width="150">Jumlah Pemilik Rekening</td><td width="10">:</td><td>{{ $jumlahPenabungAll }} Siswa</td></tr>
            <tr><td>Total Saldo Tunai</td><td>:</td><td>Rp {{ number_format($jumlahSaldoTunaiAll, 0, ',', '.') }}</td></tr>
            <tr><td>Total Saldo Digital</td><td>:</td><td>Rp {{ number_format($jumlahSaldoDigitalAll, 0, ',', '.') }}</td></tr>
        </table>
    @endif

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode</th>
                <th>Nama Siswa</th>
                <th>Rombel</th>
                <th>Saldo Riil</th>
                <th>Sisa Limit Penarikan</th>
                <th>Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $item->user?->rombel?->first()?->pivot?->kode ?? $item->user?->rombels?->first()?->pivot?->kode ?? $item->user?->username }}</td>
                    <td class="fw-medium">{{ $item->user?->name }}</td>
                    <td class="text-center">{{ $item->user?->rombel?->first()?->name ?? $item->user?->rombels?->first()?->name ?? '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->sisa, 0, ',', '.') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->updated_at)->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ditemukan data siswa yang memiliki saldo aktif pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td>
                <p>Cianjur, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <p>Bendahara Sekolah</p>
                <br><br><br>
                <p><strong>Tresna Komala, S.Pd</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>