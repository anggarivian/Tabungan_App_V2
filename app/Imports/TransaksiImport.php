<?php

namespace App\Imports;

use App\Models\Transaksi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransaksiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Transaksi([
            'jumlah_transaksi' => $row['jumlah_transaksi'],
            'saldo_awal'       => $row['saldo_awal'],
            'saldo_akhir'      => $row['saldo_akhir'],
            'tipe_transaksi'   => $row['tipe_transaksi'],
            'pembayaran'       => $row['pembayaran'],
            'pembuat'          => $row['pembuat'],
            'checkout_link'    => null,
            'external_id'      => null,
            'status'           => 'success',
            'token_stor'       => \Illuminate\Support\Str::random(10),
            'user_id'          => $row['user_id'],
            'tabungan_id'      => $row['tabungan_id'],
        ]);
    }
}
