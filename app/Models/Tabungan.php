<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaksi;

/**
 * Model untuk mengelola data tabungan.
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Database\Eloquent\Relations\BelongsTo $user
 * @property \Illuminate\Database\Eloquent\Relations\HasMany $transaksis
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tabungan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tabungan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tabungan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tabungan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tabungan whereUserId($value)
 */
class Tabungan extends Model
{
    use HasFactory;

    /**
     * Tidak ada atribut yang dapat diisi massal karena menggunakan $guarded untuk mengatur atribut yang tidak dapat diisi.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Mendapatkan pengguna yang terkait dengan tabungan ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan transaksi yang terkait dengan tabungan ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
