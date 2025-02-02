<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransaksiExport implements FromCollection, WithHeadings
{
    protected $transaksi;
    protected $exportType;

    public function __construct($transaksi, $exportType)
    {
        $this->transaksi = $transaksi;
        $this->exportType = $exportType;
    }

    public function collection()
    {
        return $this->transaksi->map(function($transaksi) {
                $data = [
                    'no' => $index + 1,
                    'id' => $transaksi->user->username,
                    'nama' => $transaksi->user->name,
                    'kelas' => $transaksi->user->kelas->name,
                    'saldo_awal' => $transaksi->saldo_awal,
                    'jumlah_transaksi' => $transaksi->jumlah_transaksi,
                    'saldo_akhir' => $transaksi->saldo_akhir,
                    'tipe_transaksi' => $transaksi->tipe_transaksi,
                    'pembayaran' => $transaksi->pembayaran,
                    'pembuat' => $transaksi->pembuat,
                    'created_at' => $transaksi->created_at->format('Y-m-d H:i:s'),
                ];

            return $data;
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Tabungan',
            'Nama',
            'Kelas',
            'Saldo Awal',
            'Jumlah Transaksi',
            'Saldo Akhir',
            'Tipe Transaksi',
            'Pembayaran',
            'Pembuat',
            'Tanggal Dibuat',
        ];
    }
}
