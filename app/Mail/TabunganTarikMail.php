<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganTarikMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaksi;

    public function __construct($user, $transaksi)
    {
        $this->user = $user;
        $this->transaksi = $transaksi;
    }

    public function build()
    {
        return $this->subject('Bukti Stor Tabungan')
                    ->view('emails.tabungan_tarik');
    }
}
