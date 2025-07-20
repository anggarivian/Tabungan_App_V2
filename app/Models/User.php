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

    public function tabungan()
    {
        return $this->hasOne(Tabungan::class, 'user_id');
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
