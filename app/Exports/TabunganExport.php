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
        return $this->siswas->map(function($tabungan, $index) {
            
            // Hitung bersih Tunai (Stor - Tarik di periode ini)
            $storTunai = \App\Models\Transaksi::where('user_id', $tabungan->user_id)->where('periode_id', $tabungan->periode_id)->where('pembayaran', 'Tunai')->where('tipe_transaksi', 'Stor')->where('status', 'success')->sum('jumlah_transaksi');
            $tarikTunai = \App\Models\Transaksi::where('user_id', $tabungan->user_id)->where('periode_id', $tabungan->periode_id)->where('pembayaran', 'Tunai')->where('tipe_transaksi', 'Tarik')->where('status', 'success')->sum('jumlah_transaksi');
            
            // Hitung bersih Digital (Stor - Tarik di periode ini)
            $storDigital = \App\Models\Transaksi::where('user_id', $tabungan->user_id)->where('periode_id', $tabungan->periode_id)->where('pembayaran', 'Digital')->where('tipe_transaksi', 'Stor')->where('status', 'success')->sum('jumlah_transaksi');
            $tarikDigital = \App\Models\Transaksi::where('user_id', $tabungan->user_id)->where('periode_id', $tabungan->periode_id)->where('pembayaran', 'Digital')->where('tipe_transaksi', 'Tarik')->where('status', 'success')->sum('jumlah_transaksi');

            return [
                'no'                 => $index + 1,
                'id'                 => optional($tabungan->user->rombel->first())->pivot->kode ?? $tabungan->user->username,
                'nama'               => $tabungan->user->name,
                'rombel'             => optional($tabungan->user->rombel->first())->name ?? '-',
                'saldo_tunai'        => $storTunai - $tarikTunai,
                'saldo_digital'      => $storDigital - $tarikDigital,
                'terakhir_transaksi' => optional($tabungan->user->transaksi()->latest('updated_at')->first())->updated_at,
                'dapat_ditarik'      => $tabungan->sisa,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Rombel', 'Nama', 'Rombel', 'Saldo Tunai', 
            'Saldo Digital', 'Terakhir Transaksi', 'Dapat Ditarik',
        ];
    }
}