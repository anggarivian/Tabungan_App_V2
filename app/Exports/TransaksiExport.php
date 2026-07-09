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
        return $this->transaksi->map(function($transaksi, $index) {
            return [
                'no'               => $index + 1,
                'id'               => optional($transaksi->user->rombel->first())->pivot->kode ?? $transaksi->user->username,
                'nama'             => $transaksi->user->name,
                'rombel'           => optional($transaksi->user->rombel->first())->name ?? '-',
                'saldo_awal'       => $transaksi->saldo_awal,
                'jumlah_transaksi' => $transaksi->jumlah_transaksi,
                'saldo_akhir'      => $transaksi->saldo_akhir,
                'tipe_transaksi'   => $transaksi->tipe_transaksi,
                'pembayaran'       => $transaksi->pembayaran,
                'pembuat'          => $transaksi->pembuat,
                'status'           => $transaksi->status,
                'created_at'       => $transaksi->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Rombel', 'Nama', 'Rombel', 'Saldo Awal', 'Jumlah Transaksi', 
            'Saldo Akhir', 'Tipe Transaksi', 'Pembayaran', 'Pembuat', 'Status', 'Tanggal Dibuat',
        ];
    }
}