<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Pengajuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .headee{
          margin: 0px;
        }
        hr {
            border: 1px solid #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #688889;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3 class="headee">DINAS PENDIDIKAN PEMUDA DAN OLAHRAGA</h3>
        <h4 class="headee">KABUPATEN CIANJUR</h4>
        <h2 class="headee">SEKOLAH DASAR NEGERI SUKARAME</h2>
        <h4 class="headee">KECAMATAN SUKANAGARA</h4>
    </div>
    <hr>
    <h3>Laporan Tabungan @if($exportOption == 'kelas') Kelas {{$kelasId}} @elseif($exportOption == 'semua') Semua Kelas @endif </h3>

    @if($exportOption == 'siswa')
    <table>
        <tr><td>ID Tabungan</td><td>{{ $user->id }}</td></tr>
        <tr><td>Nama</td><td>{{ $user->name }}</td></tr>
        <tr><td>Kelas</td><td>{{ $user->kelas->name }}</td></tr>
        <tr><td>Kontak</td><td>{{ $user->kontak }}</td></tr>
        <tr><td>Jumlah Tabungan Tunai</td><td>Rp. {{ number_format($user->tabungan->saldo, 0, ',', '.') }}</td></tr>
        <tr><td>Jumlah Tabungan Digital</td><td>Rp. {{ number_format($user->tabungan->saldo, 0, ',', '.') }}</td></tr>
    </table>
    @elseif($exportOption == 'kelas')
        <table>
            <tr><td>Kelas</td><td>:</td><td>{{ $kelasId }}</td></tr>
            <tr><td>Jumlah Penabung</td><td>:</td><td>{{ $jumlahPenabung }}</td></tr>
            <tr><td>Total Tabungan Tunai</td><td>:</td><td>Rp. {{ number_format($jumlahSaldoTunai, 0, ',', '.') }}</td></tr>
            <tr><td>Total Tabungan Digital</td><td>:</td><td>Rp. {{ number_format($jumlahSaldoDigital, 0, ',', '.') }}</td></tr>
        </table>
    @elseif($exportOption == 'semua')
        <table>
            <tr><td>Jumlah Penabung</td><td>:</td><td>{{ $jumlahPenabungAll }}</td></tr>
            <tr><td>Total Tabungan Tunai</td><td>:</td><td>Rp. {{ number_format($jumlahSaldoTunaiAll, 0, ',', '.') }}</td></tr>
            <tr><td>Total Tabungan Digital</td><td>:</td><td>Rp. {{ number_format($jumlahSaldoDigitalAll, 0, ',', '.') }}</td></tr>
        </table>
    @endif

    @if($exportOption == 'kelas' || $exportOption == 'semua')
    <table>
        <thead>
            <tr>
                <th>No</th>
                @if($exportOption == 'kelas')<th>Nama</th>@endif
                @if($exportOption == 'semua')<th>Nama</th><th>Kelas</th>@endif
                <th>Saldo Tunai</th>
                <th>Saldo Digital</th>
                <th>Dapat Ditarik</th>
                <th>Terakhir Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                @if($exportOption == 'kelas')<td>{{$item->user->name}}</td>@endif
                @if($exportOption == 'semua')<td>{{ $item->user->name }}</td><td>{{ $item->user->kelas->name }}</td>@endif
                <td>{{ number_format($item->saldo) }}</td>
                <td>{{ number_format($item->saldo) }}</td>
                <td>{{ number_format($item->sisa) }}</td>
                <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d m Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Cianjur, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <br><br>
        <p><strong>SUYATNA</strong></p>
    </div>
</body>
</html>
