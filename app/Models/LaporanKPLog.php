<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKPLog extends Model
{
    use HasFactory;

    protected $table = 'laporan_kp_logs';
    
    public $timestamps = false;

    protected $fillable = [
        'laporan_kp_id',
        'dosen_id',
        'aksi',
        'catatan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function laporan()
    {
        return $this->belongsTo(LaporanKP::class, 'laporan_kp_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
