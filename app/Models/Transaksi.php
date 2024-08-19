<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk mengelola data transaksi.
 *
 * @property int $id
 * @property \Illuminate\Database\Eloquent\Relations\HasMany $transaksis
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Transaksi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaksi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaksi query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaksi whereId($value)
 */
class Transaksi extends Model
{
    use HasFactory;

    /**
     * Mendapatkan transaksi yang terkait dengan transaksi ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
