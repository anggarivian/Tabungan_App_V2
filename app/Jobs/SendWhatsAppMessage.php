<?php
namespace App\Jobs;

use App\Models\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected Transaksi $transaksi;

    public function __construct(Transaksi $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    public function handle()
    {
        $t = $this->transaksi;
        $u = $t->user;
    
        // Lewati jika kontak tidak tersedia
        if (empty($u->kontak)) {
            Log::info("WA SKIPPED: User ID {$u->id} ({$u->name}) tidak memiliki kontak.");
            return;
        }
        
        $kelasName = optional($u->kelas)->name ?? '-';
        $tgl = \Carbon\Carbon::now()->isoFormat('D MMMM YYYY');
        $isStor = strtolower($t->tipe_transaksi) === 'stor';
        $action = $isStor ? 'Stor' : 'Tarik';
        $fmt = fn($n) => number_format($n, 0, ',', '.');
    
        $msg = "Halo, *{$u->name}* 👋\n";
        $msg .= "- Kelas {$kelasName} - {$tgl}\n\n";
        $msg .= "*{$action} Tabungan* Anda Berhasil :\n\n";
        $msg .= "🔹 Saldo Awal      : Rp {$fmt($t->saldo_awal)}\n";
        $msg .= "🔹 *Jumlah {$action}   : Rp {$fmt($t->jumlah_transaksi)}*\n";
        $msg .= "🔹 Saldo Akhir     : Rp {$fmt($t->saldo_akhir)}\n\n";
        $msg .= "🙏 Terima kasih.\n";
        $msg .= "🔗 Detail: sukarame-tabungan-siswa.my.id\n";
        $msg .= "⚠️ _Pesan otomatis. Jangan dibalas._";
    
        try {
            $token = config('services.wablas.token');
            $secret = config('services.wablas.secret_key');
            $endpoint = config('services.wablas.endpoint');
    
            $payload = ['data' => [[
                'phone' => $this->formatPhone($u->kontak),
                'message' => $msg,
                'isGroup' => 'false',
            ]]];
    
            $resp = \Http::timeout(5)->retry(2, 100)
                ->withHeaders([
                    'Authorization' => "{$token}.{$secret}",
                    'Content-Type' => 'application/json',
                ])->post($endpoint, $payload);
    
            if (! $resp->ok() || ! data_get($resp->json(), 'status')) {
                \Log::error('WA gagal: ' . $resp->body());
            }
        } catch (\Exception $e) {
            \Log::error('Error kirim WA: ' . $e->getMessage());
        }
    }

    protected function formatPhone(string $kontak): string
    {
        $digits = preg_replace('/\D+/','',$kontak);
        if (str_starts_with($digits,'0')) {
            return '62'.substr($digits,1);
        } elseif (str_starts_with($digits,'8')) {
            return '62'.$digits;
        }
        return $digits;
    }
}
