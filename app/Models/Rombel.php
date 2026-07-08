<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $fillable = ['name', 'walikelas_id', 'kelas_id', 'periode_id'];

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'rombel_user')
            ->withPivot('no_absen') 
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'rombel_user', 'rombel_id', 'user_id')
                    ->withPivot('no_absen')
                    ->withTimestamps();
    }

    public function walikelas()
    {
        return $this->belongsTo(User::class, 'walikelas_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}
