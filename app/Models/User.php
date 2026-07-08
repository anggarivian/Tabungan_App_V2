<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Kelas;
use App\Models\Tabungan;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tabungans()
    {
        return $this->hasMany(Tabungan::class, 'user_id');
    }

    public function tabungan()
    {
        return $this->hasOne(Tabungan::class)
            ->where('periode_id', \App\Helpers\PeriodeHelper::id());
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'user_id');
    }

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'user_id');
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }
    
    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'rombel_user', 'user_id', 'rombel_id')
            ->withPivot('kode')
            ->withTimestamps();
    }

    public function rombel()
    {
        return $this->belongsToMany(Rombel::class, 'rombel_user', 'user_id', 'rombel_id')
            ->withPivot('kode')
            ->withTimestamps();
    }

    /**
     * Cari siswa berdasarkan kode (misal "1A05") di periode tertentu.
     * Berguna buat nunjuk data tabungan/transaksi/pengajuan dari kode.
     */
    public static function findByKode(string $kode, ?int $periodeId = null): ?self
    {
        $periodeId = $periodeId ?? \App\Helpers\PeriodeHelper::id();

        return static::whereHas('rombel', function ($q) use ($kode, $periodeId) {
            $q->where('rombels.periode_id', $periodeId)
            ->where('rombel_user.kode', strtoupper(trim($kode)));
        })->first();
    }

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
