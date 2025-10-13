<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Kelas extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'id', 'kelas_id');
    }
}
