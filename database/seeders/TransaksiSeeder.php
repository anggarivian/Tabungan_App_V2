<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Tabungan;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $bendaharaName = 'Operator Bendahara';

        $siswaList = User::where('roles_id', 4)->get();

        if ($siswaList->isEmpty()) {
            $this->command->warn('❌ Tidak ada siswa ditemukan. Seeder Transaksi dilewati.');
            return;
        }

        foreach ($siswaList as $siswa) {
            $tabungan = $siswa->tabungan;
            if (!$tabungan) continue;

            $saldo = $tabungan->saldo ?? 0;

            $months = [
                ['month' => 8, 'name' => 'Agustus'],
                ['month' => 9, 'name' => 'September'],
                ['month' => 10, 'name' => 'Oktober'],
            ];

            DB::transaction(function () use ($months, $siswa, $tabungan, $faker, $bendaharaName, &$saldo) {
                foreach ($months as $m) {
                    $baseDate = Carbon::create(2025, $m['month'], 1);

                    // 2 Stor (deposit)
                    for ($i = 0; $i < 2; $i++) {
                        $stor = $faker->numberBetween(2000, 10000);
                        $awal = $saldo;
                        $akhir = $awal + $stor;

                        // same formula as your controller
                        $premi = $akhir * 0.025;
                        $sisa = $akhir - $premi;

                        $tabungan->update([
                            'saldo' => $akhir,
                            'premi' => $premi,
                            'sisa'  => $sisa,
                        ]);

                        Transaksi::create([
                            'jumlah_transaksi' => $stor,
                            'saldo_awal' => $awal,
                            'saldo_akhir' => $akhir,
                            'tipe_transaksi' => 'Stor',
                            'pembayaran' => 'Tunai',
                            'pembuat' => $bendaharaName,
                            'status' => 'success',
                            'token_stor' => Str::random(10),
                            'user_id' => $siswa->id,
                            'tabungan_id' => $tabungan->id,
                            'created_at' => $baseDate->copy()->addDays(rand(1, 25))->addHours(rand(8, 15)),
                            'updated_at' => $baseDate->copy()->addDays(rand(1, 25))->addHours(rand(8, 15)),
                        ]);

                        $saldo = $akhir;
                    }

                    // 1 Tarik (withdraw)
                    if ($saldo > 0) {
                        $tarik = $faker->numberBetween(1000, min(5000, $saldo));
                        $awal = $saldo;
                        $akhir = $awal - $tarik;

                        $premi = $akhir * 0.025;
                        $sisa  = $akhir - $premi;

                        $tabungan->update([
                            'saldo' => $akhir,
                            'premi' => $premi,
                            'sisa'  => $sisa,
                        ]);

                        Transaksi::create([
                            'jumlah_transaksi' => $tarik,
                            'saldo_awal' => $awal,
                            'saldo_akhir' => $akhir,
                            'tipe_transaksi' => 'Tarik',
                            'pembayaran' => 'Tunai',
                            'pembuat' => $bendaharaName,
                            'status' => 'success',
                            'token_stor' => Str::random(10),
                            'user_id' => $siswa->id,
                            'tabungan_id' => $tabungan->id,
                            'created_at' => $baseDate->copy()->addDays(rand(10, 28))->addHours(rand(8, 15)),
                            'updated_at' => $baseDate->copy()->addDays(rand(10, 28))->addHours(rand(8, 15)),
                        ]);

                        $saldo = $akhir;
                    }
                }
            });
        }

        $this->command->info('✅ TransaksiSeeder selesai dengan Stor & Tarik sesuai logika bendahara_storMasalTabungan.');
    }
}