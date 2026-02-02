<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanKP extends Model
{
    protected $table = 'pengajuan_kp';

    protected $fillable = [
        'user_id',
        'perusahaan_tujuan',
        'file_surat_pengajuan',
        'file_surat_penerimaan',
        'status',
        'tanggal_pengajuan',
        'mitra_dengan_perusahaan',
        'periode',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
