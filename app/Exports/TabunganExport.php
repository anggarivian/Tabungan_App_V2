<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TabunganExport implements FromCollection, WithHeadings
{
    protected $siswas;
    protected $exportType;

    public function __construct($siswas, $exportType)
    {
        $this->siswas = $siswas;
        $this->exportType = $exportType;
    }

    public function collection()
    {
        return $this->siswas->map(function($siswas, $index) {
                $data = [
                    'no' => $index + 1,
                    'id' => $siswas->user->username,
                    'nama' => $siswas->user->name,
                    'kelas' => $siswas->user->kelas->name,
                    'saldo_tunai' => \App\Models\Transaksi::where('user_id', $siswas->user_id)
                                    ->where('pembayaran', 'Tunai')
                                    ->sum('jumlah_transaksi'),
                    'saldo_digital' => \App\Models\Transaksi::where('user_id', $siswas->user_id)
                                    ->where('pembayaran', 'Digital')
                                    ->sum('jumlah_transaksi'),
                    'terakhir_transaksi' => optional($siswas->user->transaksi()->latest('updated_at')->first())->updated_at,
                    'dapat_ditarik' => $siswas->sisa,
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
            'Saldo Tunai',
            'Saldo Digital',
            'Terakhir Transaksi',
            'Dapat Ditarik',
        ];
    }
}
