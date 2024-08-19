<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Model untuk mengelola data role.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Tidak ada atribut yang dapat diisi massal karena menggunakan $guarded untuk mengatur atribut yang tidak dapat diisi.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Mendapatkan pengguna yang terkait dengan role ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
