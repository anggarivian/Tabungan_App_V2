<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tabungan;

class Pengajuan extends Model
{
    use HasFactory;

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
