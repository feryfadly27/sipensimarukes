<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'no_pendaftaran',
        'no_identitas',
        'no_telp',
        'nama',
        'jenis_kelamin',
        'prodi',
        'prodi_pilihan_1',
        'prodi_pilihan_2',
        'tempat_lahir',
        'tanggal_lahir',
        'asal_sekolah',
        'alamat',
        'status_kehadiran',
        'nomor_urut',
        'foto_kehadiran',
        'status_plp',
        'status_dokter',
        'kesimpulan_akhir',
        'keterangan_kesimpulan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke tabel pemeriksaan_plp
     */
    public function pemeriksaanPlp()
    {
        return $this->hasOne(PemeriksaanPlp::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke tabel pemeriksaan_dokter
     */
    public function pemeriksaanDokter()
    {
        return $this->hasOne(PemeriksaanDokter::class, 'mahasiswa_id');
    }

    /**
     * Scope untuk filter berdasarkan status kehadiran
     */
    public function scopeHadir($query)
    {
        return $query->where('status_kehadiran', 'hadir');
    }

    public function scopeBelumHadir($query)
    {
        return $query->where('status_kehadiran', 'belum_konfirmasi');
    }

    /**
     * Scope untuk filter berdasarkan status PLP
     */
    public function scopePlpSelesai($query)
    {
        return $query->where('status_plp', 'selesai');
    }

    public function scopePlpBelum($query)
    {
        return $query->where('status_plp', 'belum');
    }

    /**
     * Scope untuk filter berdasarkan status Dokter
     */
    public function scopeDokterSelesai($query)
    {
        return $query->where('status_dokter', 'selesai');
    }

    public function scopeDokterBelum($query)
    {
        return $query->where('status_dokter', 'belum');
    }

    /**
     * Scope untuk antrian PLP (hadir dan belum diperiksa PLP)
     */
    public function scopeAntrianPlp($query)
    {
        return $query->where('status_kehadiran', 'hadir')
                     ->where('status_plp', 'belum');
    }

    /**
     * Scope untuk antrian Dokter (PLP selesai dan belum diperiksa Dokter)
     */
    public function scopeAntrianDokter($query)
    {
        return $query->where('status_plp', 'selesai')
                     ->where('status_dokter', 'belum');
    }

    /**
     * Accessor untuk status kehadiran dalam format readable
     */
    public function getStatusKehadiranTextAttribute()
    {
        return match($this->status_kehadiran) {
            'belum_konfirmasi' => 'Belum Konfirmasi',
            'hadir' => 'Hadir',
            'tidak_hadir' => 'Tidak Hadir',
            default => '-'
        };
    }

    /**
     * Accessor untuk kesimpulan akhir dalam format readable
     */
    public function getKesimpulanAkhirTextAttribute()
    {
        return match($this->kesimpulan_akhir) {
            'memenuhi_syarat' => 'Memenuhi Syarat',
            'tidak_memenuhi_syarat' => 'Tidak Memenuhi Syarat',
            default => '-'
        };
    }
}
