<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'kelas_id', 'walikelas_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function walikelas()
    {
        return $this->hasOne(User::class, 'rombel_id')->where('roles_id', 3);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'rombel_id');
    }

    public function siswa()
    {
        return $this->hasMany(User::class, 'rombel_id')->where('roles_id', 4);
    }
}
