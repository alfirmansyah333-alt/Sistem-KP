<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanKP extends Model
{
    protected $table = 'penerimaan_kps';

    protected $fillable = [
        'user_id',
        'pengajuan_kp_id',
        'perusahaan_id',
        'nama_perusahaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_surat_penerimaan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuanKP()
    {
        return $this->belongsTo(PengajuanKP::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
