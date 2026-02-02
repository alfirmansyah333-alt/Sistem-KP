<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'is_mitra',
    ];

    protected $casts = [
        'is_mitra' => 'boolean',
    ];
}
