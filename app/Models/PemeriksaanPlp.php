<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemeriksaanPlp extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_plp';

    protected $fillable = [
        'mahasiswa_id',
        'plp_id',
        'tgl_periksa',
        'riwayat_penyakit',
        'suhu',
        'tensi',
        'riwayat_keluarga',
        'keterangan_pemeriksaan',
        'catatan_warning_dokter',
        'buta_warna',
        'tinggi_badan',
        'berat_badan',
        'bmi',
        'status_pemeriksaan',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'tgl_periksa' => 'datetime',
        'suhu' => 'decimal:1',
        'tinggi_badan' => 'decimal:2',
        'berat_badan' => 'decimal:2',
        'bmi' => 'decimal:2',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke tabel users (PLP)
     */
    public function plp()
    {
        return $this->belongsTo(User::class, 'plp_id');
    }

    /**
     * Hitung BMI otomatis
     */
    public function hitungBmi()
    {
        if ($this->tinggi_badan && $this->berat_badan) {
            $tinggiMeter = $this->tinggi_badan / 100;
            $this->bmi = $this->berat_badan / ($tinggiMeter * $tinggiMeter);
            return $this->bmi;
        }
        return null;
    }

    /**
     * Accessor untuk kategori BMI
     */
    public function getKategoriBmiAttribute()
    {
        if (!$this->bmi) return '-';

        if ($this->bmi < 18.5) return 'Underweight';
        if ($this->bmi < 25) return 'Normal';
        if ($this->bmi < 30) return 'Overweight';
        return 'Obesitas';
    }

    /**
     * Accessor untuk status tinggi badan
     */
    public function getStatusTinggiBadanAttribute()
    {
        if (!$this->tinggi_badan) return '-';

        $mahasiswa = $this->mahasiswa;
        if (!$mahasiswa) return '-';

        if ($mahasiswa->jenis_kelamin === 'L') {
            return $this->tinggi_badan >= 160 ? 'Memenuhi syarat' : 'Tidak memenuhi syarat';
        } else {
            return $this->tinggi_badan >= 150 ? 'Memenuhi syarat' : 'Tidak memenuhi syarat';
        }
    }
}
