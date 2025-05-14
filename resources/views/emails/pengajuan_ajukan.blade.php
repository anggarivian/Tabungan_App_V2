<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Penarikan</title>
</head>
<body style="margin: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b;">

<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);">
    <div style="background-color: #1d4ed8; padding: 32px; text-align: center;">
        <h1 style="color: #ffffff; font-size: 24px; margin: 0;">Pengajuan Penarikan</h1>
        <p style="color: #c7d2fe; font-size: 15px; margin-top: 10px;">Mohon tunggu persetujuan dari Bendahara</p>
    </div>

    <div style="padding: 40px 32px;">
        <p style="font-size: 16px; margin-bottom: 24px; line-height: 1.7;">
            Yth. <strong>{{ $user->name }}</strong>,<br><br>
            Anda telah mengajukan penarikan tabungan. Berikut rincian pengajuan Anda:
        </p>

        <table style="width: 100%; font-size: 15px; border-collapse: collapse; margin-bottom: 32px;">
            <tr>
                <td style="padding: 10px 0;">ID Tabungan</td>
                <td style="padding: 10px 0; text-align: right;">{{ $user->username }}</td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">Nama</td>
                <td style="padding: 10px 0; text-align: right;">{{ $user->name }}</td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">Kelas</td>
                <td style="padding: 10px 0; text-align: right;">{{ $user->kelas->name }}</td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">Saldo Awal</td>
                <td style="padding: 10px 0; text-align: right;">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">Jumlah Tarik</td>
                <td style="padding: 10px 0; text-align: right;">Rp {{ number_format($pengajuan->jumlah_penarikan, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">Alasan</td>
                <td style="padding: 10px 0; text-align: right;">{{ $pengajuan->alasan }}</td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 10px 0;">Pembayaran</td>
                <td style="padding: 10px 0; text-align: right;">{{ $pengajuan->pembayaran }}</td>
            </tr>
            @if($pengajuan->pembayaran === 'Digital')
                @if($pengajuan->metode_digital === 'bank_transfer')
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Bank</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $pengajuan->bank_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;">No. Rekening</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $pengajuan->nomor_rekening }}</td>
                    </tr>
                @elseif($pengajuan->metode_digital === 'ewallet')
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">e-Wallet</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $pengajuan->ewallet_type }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;">Nomor e-Wallet</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $pengajuan->ewallet_number }}</td>
                    </tr>
                @endif
            @endif
        </table>

        <div style="text-align: center;">
            <a href="{{ url('/siswa/tabungan/tarik') }}" style="background-color: #1d4ed8; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 15px;">
                Lihat Detail Pengajuan
            </a>
        </div>

        <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 40px;">
            © 2025 Tabungan SakuRame - SDN Sukarame
        </p>
    </div>

    <div style="background-color: #f8fafc; padding: 24px; text-align: center; font-size: 12px; color: #64748b;">
        Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
    </div>
</div>

</body>
</html>
