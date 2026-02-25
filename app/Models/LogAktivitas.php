<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'aksi',
        'target_id',
        'target_tabel',
        'waktu',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk filter berdasarkan aksi
     */
    public function scopeByAksi($query, $aksi)
    {
        return $query->where('aksi', $aksi);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tabel target
     */
    public function scopeByTargetTabel($query, $tabel)
    {
        return $query->where('target_tabel', $tabel);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $tanggal)
    {
        return $query->whereDate('waktu', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('waktu', [$start, $end]);
    }

    /**
     * Helper method untuk mencatat aktivitas
     */
    public static function catat($aksi, $targetId = null, $targetTabel = null, $dataLama = null, $dataBaru = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'target_id' => $targetId,
            'target_tabel' => $targetTabel,
            'waktu' => now(),
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Accessor untuk deskripsi aktivitas yang lebih readable
     */
    public function getDeskripsiAttribute()
    {
        $userName = $this->user ? $this->user->nama : 'User tidak diketahui';
        $aksi = $this->aksi;
        $waktu = $this->waktu->format('d/m/Y H:i');
        
        return "{$userName} melakukan {$aksi} pada {$waktu}";
    }
}
