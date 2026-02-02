<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nim',
        'nidn',
        'email',
        'password',
        'roles',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }

    public function pengajuanKP()
    {
        return $this->hasMany(PengajuanKP::class);
    }

    public function penerimaanKP()
    {
        return $this->hasMany(PenerimaanKP::class);
    }

    public function seminarKP()
    {
        return $this->hasMany(SeminarKP::class);
    }

    public function laporanKP()
    {
        return $this->hasMany(LaporanKP::class);
    }

    public function pembimbingan()
    {
        return $this->hasOne(PembimbinganKP::class, 'user_id');
    }

    public function hasRole($role)
    {
        $roles = $this->roles ?? [];
        if ($role === 'koor') {
            return in_array('koor', $roles) || in_array('koordinator', $roles);
        }
        if ($role === 'koordinator') {
            return in_array('koordinator', $roles) || in_array('koor', $roles);
        }

        return in_array($role, $roles);
    }
}
