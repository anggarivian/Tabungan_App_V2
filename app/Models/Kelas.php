<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Model untuk mengelola data kelas.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Kelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kelas query()
 * @method static \Illuminate\Database\Eloquent\Builder|Kelas whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kelas whereName($value)
 */
class Kelas extends Model
{
    use HasFactory;

    /**
     * Tidak ada atribut yang dapat diisi massal karena menggunakan $guarded untuk mengatur atribut yang tidak dapat diisi.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Mendapatkan pengguna yang terkait dengan kelas ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
