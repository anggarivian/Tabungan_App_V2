<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganAjukan extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pengajuan;
    public $saldoAwal;

    public function __construct($user, $pengajuan, $saldoAwal)
    {
        $this->user = $user;
        $this->pengajuan = $pengajuan;
        $this->saldoAwal = $saldoAwal;
    }

    public function build()
    {
        return $this->subject('Pengajuan Penarikan Tabungan')
            ->view('emails.pengajuan-notifikasi');
    }
}
