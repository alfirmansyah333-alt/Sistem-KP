<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKP extends Model
{
    protected $table = 'laporan_kp';

    protected $fillable = [
        'user_id',
        'judul_kp_final',
        'file_laporan',
        'tanggal_upload',
        'status_approve',
        'nilai',
        'catatan_dosen',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    /**
     * Relasi ke mahasiswa (user)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
