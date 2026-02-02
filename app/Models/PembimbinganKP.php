<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembimbinganKP extends Model
{
    protected $table = 'pembimbingan_kp';

    protected $fillable = [
        'user_id',
        'koordinator_id',
        'dosen_pembimbing_id',
            'mentor_perusahaan',
        'catatan',
    ];

    // Relasi ke mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke koordinator
    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    // Relasi ke dosen pembimbing
    public function dosenPembimbing()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    // Relasi ke penerimaan KP yang diterima (untuk ambil nama perusahaan)
    public function penerimaanKP()
    {
        return $this->hasOneThrough(
            PenerimaanKP::class,
            User::class,
            'id', // Foreign key di users
            'user_id', // Foreign key di penerimaan_kp
            'user_id', // Local key di pembimbingan_kp
            'id' // Local key di users
        )->where('status', 'diterima');
    }
}
