<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];
}
