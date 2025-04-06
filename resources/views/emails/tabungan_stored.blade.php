<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Stor Tabungan</title>
</head>
<body style="margin: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);">

        <div style="background-color: #1d4ed8; padding: 32px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 24px; margin: 0;">Invoice Stor Tabungan</h1>
            <p style="color: #c7d2fe; font-size: 15px; margin-top: 10px;">Transaksi telah berhasil diproses</p>
        </div>

        <div style="padding: 40px 32px;">
            <p style="font-size: 16px; margin-bottom: 24px; line-height: 1.7;">
                Yth. <strong>{{ $user->name }}</strong>,
                <br><br>
                Berikut adalah rincian transaksi tabungan Anda:
            </p>

            <table style="width: 100%; font-size: 15px; border-collapse: collapse; margin-bottom: 32px;">
                <tbody>
                    <tr>
                        <td style="padding: 10px 0;">ID Tabungan</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">{{ $user->username }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Nama</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">{{ $user->name }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Kelas</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">{{ $user->kelas->name }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Saldo Awal</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">Rp {{ number_format($transaksi->saldo_awal, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Jumlah Stor</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">
                            <span style="
                                background-color: #1d4ed8;
                                color: white;
                                padding: 6px 12px;
                                border-radius: 9999px;
                                font-size: 16px;
                                font-weight: bold;
                                display: inline-block;
                            ">
                                Rp {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}
                            </span>
                        </td>

                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Saldo Akhir</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">Rp {{ number_format($transaksi->saldo_akhir, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">Keterangan</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: 600;">{{ $transaksi->tipe_transaksi }} , {{ $transaksi->pembayaran }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ url('/tabungan/'.$user->id) }}" style="background-color: #1d4ed8; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 15px;">
                    Lihat Detail
                </a>
            </div>

            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 40px;">
                © 2025 Tabungan SakuRame - SDN Sukarame - Jl. Raya Sukanagara-Pagelaran, Km 8, Sukanagara, Cianjur, Indonesia
            </p>

        </div>

        <div style="background-color: #f8fafc; padding: 24px; text-align: center; font-size: 12px; color: #64748b;">
            Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
        </div>

    </div>

</body>
</html>
