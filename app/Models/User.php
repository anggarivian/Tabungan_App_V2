<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Tabungan;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Tidak ada atribut yang dapat diisi massal karena menggunakan $guarded untuk mengatur atribut yang tidak dapat diisi.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Mendapatkan role yang terkait dengan user ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Mendapatkan kelas yang terkait dengan user ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Mendapatkan tabungan yang terkait dengan user ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tabungan()
    {
        return $this->hasOne(Tabungan::class, 'user_id');
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
