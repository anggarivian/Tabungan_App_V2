<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Transaksi</title>
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

    <div class="title-report">Laporan Mutasi Transaksi Buku Tabungan</div>

    @if($exportOption == 'siswa' && $user)
        <table class="meta-table">
            <tr><td width="150">Kode Siswa (ID)</td><td width="10">:</td><td>{{ optional($user->rombel->first())->pivot->kode ?? $user->username }}</td></tr>
            <tr><td>Nama Siswa</td><td>:</td><td>{{ $user->name }}</td></tr>
            <tr><td>Rombel</td><td>:</td><td>{{ optional($user->rombel->first())->name ?? '-' }}</td></tr>
        </table>
    @else
        <table class="meta-table">
            <tr><td width="150">Total Penabung Aktif</td><td width="10">:</td><td>{{ $jumlahPenabungAll }} Orang</td></tr>
            <tr><td>Total Kumulatif Tunai</td><td>:</td><td>Rp {{ number_format($jumlahSaldoTunaiAll, 0, ',', '.') }}</td></tr>
        </table>
    @endif

    <table class="main-table">
        <thead>
            <tr>
                <th width="30">No</th>
                @if($exportOption == 'semua')
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Rombel</th>
                @endif
                <th>Saldo Awal</th>
                <th>Mutasi Nominal</th>
                <th>Saldo Akhir</th>
                <th>Jenis</th>
                <th>Petugas</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    @if($exportOption == 'semua')
                        <td class="text-center">{{ optional($item->user->rombel->first())->pivot->kode ?? $item->user->username }}</td>
                        <td>{{ $item->user->name }}</td>
                        <td class="text-center">{{ optional($item->user->rombel->first())->name ?? '-' }}</td>
                    @endif
                    <td class="text-end">Rp {{ number_format($item->saldo_awal, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->jumlah_transaksi, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->saldo_akhir, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->tipe_transaksi }} ({{ $item->pembayaran }})</td>
                    <td>{{ $item->pembuat }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $exportOption == 'semua' ? '9' : '6' }}" class="text-center">Belum ada transaksi terekam pada periode ini.</td>
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
                <p><strong>___________________</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>