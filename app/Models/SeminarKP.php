<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarKP extends Model
{
    protected $table = 'seminar_kp';

    protected $fillable = [
        'user_id',
        'judul_kp',
        'tanggal_seminar',
        'jam_seminar',
        'ruangan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_seminar' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
