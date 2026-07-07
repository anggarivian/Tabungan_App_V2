<?php

namespace App\Helpers;

use App\Models\Periode;

class PeriodeHelper
{
    public static function aktif()
    {
        return Periode::where('status',1)->first();
    }

    public static function id()
    {
        $periode = self::aktif();

        return $periode ? $periode->id : null;
    }

    public static function nama()
    {
        $periode = self::aktif();

        return $periode ? $periode->nama : '-';
    }
}