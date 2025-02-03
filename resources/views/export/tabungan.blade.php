<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Tabungan</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-size: 12px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .wrapper {
            width: 100%;
            max-width: 800px;
        }
        .header {
            text-align: center;
            padding: 15px;
            background: #ffffff;
            color: rgb(0, 0, 0);
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.1);
        }
        .table-container {
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .footer {
            text-align: right;
            margin-top: 15px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h2>DINAS PENDIDIKAN PEMUDA DAN OLAHRAGA</h2>
            <h3>KABUPATEN CIANJUR</h3>
            <h1>SEKOLAH DASAR NEGERI SUKARAME</h1>
            <h4>KECAMATAN SUKANAGARA</h4>
        </div>
        <div class="container">
            <h3>Laporan Tabungan @if($exportOption == 'kelas') Kelas {{$kelasId}} @elseif($exportOption == 'semua') Semua Kelas @endif </h3>

            @if($exportOption == 'siswa')
            <table>
                <tr><td>ID Tabungan</td><td>{{ $user->username }}</td></tr>
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
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            @if($exportOption == 'kelas')<th>ID</th><th>Nama</th>@endif
                            @if($exportOption == 'semua')<th>ID</th><th>Nama</th><th>Kelas</th>@endif
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
                            @if($exportOption == 'kelas')<td>{{$item->user->username}}</td><td>{{$item->user->name}}</td>@endif
                            @if($exportOption == 'semua')<td>{{$item->user->username}}</td><td>{{ $item->user->name }}</td><td>{{ $item->user->kelas->name }}</td>@endif
                            <td>{{ number_format($item->saldo) }}</td>
                            <td>{{ number_format($item->saldo) }}</td>
                            <td>{{ number_format($item->sisa) }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d m Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="footer">
                <p>Cianjur, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <p>Kepala Sekolah</p>
                <br><br>
                <p><strong>SUYATNA</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
