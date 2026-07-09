<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengajuanExport implements FromCollection, WithHeadings
{
    protected $pengajuan;
    protected $exportType;

    public function __construct($pengajuan, $exportType)
    {
        $this->pengajuan = $pengajuan;
        $this->exportType = $exportType;
    }

    public function collection()
    {
        return $this->pengajuan->map(function($pengajuan, $index) {
            return [
                'no'               => $index + 1,
                'id'               => optional($pengajuan->user->rombel->first())->pivot->kode ?? $pengajuan->user->username,
                'nama'             => $pengajuan->user->name,
                'rombel'           => optional($pengajuan->user->rombel->first())->name ?? '-',
                'saldo'            => $pengajuan->tabungan->saldo ?? 0,
                'jumlah_penarikan' => $pengajuan->jumlah_penarikan,
                'status'           => $pengajuan->status,
                'pembayaran'       => $pengajuan->pembayaran,
                'created_at'       => $pengajuan->created_at->format('Y-m-d H:i:s'),
                'alasan'           => $pengajuan->alasan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Rombel', 'Nama', 'Rombel', 'Saldo Saat Ini', 
            'Jumlah Penarikan', 'Status', 'Pembayaran', 'Tanggal', 'Alasan',
        ];
    }
}