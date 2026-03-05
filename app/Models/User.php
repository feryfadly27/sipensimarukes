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
        'nama',
        'role',
        'username',
        'password',
        'no_telp',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel pemeriksaan_plp sebagai PLP
     */
    public function pemeriksaanPlp()
    {
        return $this->hasMany(PemeriksaanPlp::class, 'plp_id');
    }

    /**
     * Relasi ke tabel pemeriksaan_dokter sebagai Dokter
     */
    public function pemeriksaanDokter()
    {
        return $this->hasMany(PemeriksaanDokter::class, 'dokter_id');
    }

    /**
     * Relasi ke tabel log_aktivitas
     */
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'user_id');
    }

    /**
     * Helper method untuk cek role
     */
    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPendaftaran()
    {
        return $this->role === 'pendaftaran';
    }

    public function isNakes()
    {
        return in_array($this->role, ['nakes', 'plp'], true);
    }

    public function isPlp()
    {
        return $this->isNakes();
    }

    public function isDokter()
    {
        return $this->role === 'dokter';
    }
}
