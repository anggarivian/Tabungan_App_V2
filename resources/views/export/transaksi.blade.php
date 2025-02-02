<html>
<head>
    <title> Laporan Data Pengajuan </title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-size: 14px;
        }
        .headee{
            width: auto;
            text-align:center;
            height: 92px;
            background: rgba(255,255,255,1);
            opacity: 1;
            position: relative;
        }
        .foo{
            width: auto;
            text-align:right;
            background: rgba(255,255,255,1);
            opacity: 1;
            position: relative;
        }
        .v1_3 {
            text-align:center;
            width: auto;
            color: rgba(0,0,0,1);
            position: absolute;
            top: 15px;
            left: 212px;
            font-family: Times New Roman;
            font-weight: Bold;
            font-size: 13px;
            opacity: 1;
            text-align: left;
        }
        .v1_4 {
            width: auto;
            text-align:center;
            color: rgba(0,0,0,1);
            position: absolute;
            top: 31px;
            left: 290px;
            font-family: Times New Roman;
            font-weight: Bold;
            font-size: 13px;
            opacity: 1;
            text-align: left;
        }
        .v1_5 {
            width: auto;
            text-align:center;
            color: rgba(0,0,0,1);
            position: absolute;
            top: 47px;
            left: 177px;
            font-family: Times New Roman;
            font-weight: Bold;
            font-size: 19px;
            opacity: 1;
            text-align: left;
        }
        .v1_6 {
            width: auto;
            text-align:center;
            color: rgba(0,0,0,1);
            position: absolute;
            top: 70px;
            left: 260px;
            font-family: Times New Roman;
            font-weight: Bold;
            font-size: 13px;
            opacity: 1;
            text-align: left;
        }
        .name {
            color: #fff;
        }
        .v6_2 {
            width: 132px;
            color: rgba(0,0,0,1);
            font-family: Inter;
            font-weight: Regular;
            font-size: 13px;
            opacity: 1;
            text-align: left;
            margin-top:20px;
        }
        .v6_3 {
            width: 94px;
            color: rgba(0,0,0,1);
            padding-right: 25px;
            font-family: Inter;
            font-weight: Regular;
            font-size: 13px;
            opacity: 1;
            text-align: left;
            margin-top:10px;
        }
        .v6_4 {
            opacity: 1;
            text-align: left;
            width: auto;
            color: rgba(0,0,0,1);
            padding-right: 30px;
            font-size: 13px;
            opacity: 1;
            text-align: left;
            margin-top:30px;
        }
        .table{
            margin-top: 10px;
        }
        h4 {
            margin-top: 120px;
            margin-bottom: 10px;
            font-family: Times New Roman;
            font-weight: Bold;
        }

        #user {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #user td, #user th {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 12px;
        }

        #user tr:nth-child(even){background-color: #f2f2f2;}

        #user tr:hover {background-color: #ddd;}

        #user th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #688889;
            color: white;
        }
    </style>
</head>
<body>
    <div class="headee">
        <span class="v1_3">DINAS PENDIDIKAN PEMUDA DAN OLAHRAGA</span>
        <span class="v1_4">KABUPATEN CIANJUR</span>
        <span class="v1_5">SEKOLAH DASAR NEGERI SUKARAME</span>
        <span class="v1_6">KECAMATAN SUKANAGARA</span>
    </div>
    <hr>
    <h3>Laporan Transaksi Tabungan @if($exportOption == 'kelas') Kelas {{$kelasId}} @elseif($exportOption == 'semua') Semua Kelas @endif </h3>
        @if($exportOption == 'siswa')
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td>{{ $user->kelas->name }}</td>
                </tr>
                <tr>
                    <td>Jumlah Tabungan</td>
                    <td>:</td>
                    <td>{{ number_format($user->tabungan->saldo, 0, ',', '.') }}</td>
                </tr>
            </table>
        @endif
        <br>
        <p>Detail Transaksi dari <span> {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</span></p>
        <table id="user" border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    @if($exportOption == 'kelas')
                        <th>Nama</th>
                    @elseif($exportOption == 'semua')
                        <th>Nama</th>
                        <th>Kelas</th>
                    @endif
                    <th>Saldo Awal</th>
                    <th>Transaksi</th>
                    <th>Saldo Akhir</th>
                    <th>Keterangan</th>
                    <th>Pembuat</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksis as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        @if($exportOption == 'kelas')
                            <td>{{$item->user->name}}</td>
                        @elseif($exportOption == 'semua')
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->user->kelas->name }}</td>
                        @endif
                        <td>{{  number_format($item->saldo_awal) }}</td>
                        <td>{{  number_format($item->jumlah_transaksi) }}</td>
                        <td>{{  number_format($item->saldo_akhir) }}</td>
                        <td>{{ $item->tipe_transaksi }}, {{ $item->pembayaran }}</td>
                        <td>{{ $item->pembuat }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d m Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="foo">
            <br>
            <span class="v6_2">Cianjur, {{ \Carbon\Carbon::now()->format('d F Y') }}</span><br>
            <span class="v6_3">Kepala Sekolah</span><br><br><br><br><br><br>
            <span class="v6_4">SUYATNA</span><br>
        </div>
    </body>
</body>
</html>
