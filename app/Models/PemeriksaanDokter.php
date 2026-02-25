<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemeriksaanDokter extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_dokter';

    protected $fillable = [
        'mahasiswa_id',
        'dokter_id',
        'tgl_periksa',
        // Item 3: Kulit
        'kulit',
        // Item 4: Mata
        'mata_kacamata',
        'mata_minus',
        'mata_minus_nilai',
        'mata_silindris',
        'mata_silindris_nilai',
        'mata_strabismus',
        'mata_strabismus_nilai',
        // Item 5: Telinga
        'telinga_kiri',
        'telinga_kiri_ket',
        'telinga_kanan',
        'telinga_kanan_ket',
        // Item 6: Hidung
        'hidung_cuping',
        'hidung_cuping_ket',
        // Item 7: Lidah
        'lidah_kebersihan',
        'lidah_kebersihan_ket',
        'lidah_stomatitis',
        'lidah_stomatitis_ket',
        // Item 8: Pharing
        'pharing_nyeri_tekan',
        'pharing_nyeri_tekan_ket',
        // Item 9: Tonsil
        'tonsil_kemerahan',
        'tonsil_kemerahan_ket',
        'tonsil_pembesaran',
        // Item 10: Gigi
        'gigi_lengkap',
        // Item 11: Tiroid
        'tiroid',
        // Item 12: Jantung
        'jantung_murmur',
        'jantung_murmur_ket',
        // Item 13: Paru-paru
        'paru_suara_tambahan',
        // Item 14: Abdomen
        'abdomen_hamil',
        // Item 15: Pupil
        'pupil',
        // Item 16: Thorax Photo
        'thorax_photo_file',
        'thorax_photo_ket',
        // Item 17: Tulang Belakang
        'tulang_skoliosis',
        'tulang_skoliosis_ket',
        'tulang_lordosis',
        'tulang_lordosis_ket',
        'tulang_kifosis',
        'tulang_kifosis_ket',
        'tulang_lainnya',
        'tulang_lainnya_ket',
        // Item 18: Bicara
        'bicara_artikulasi',
        'bicara_artikulasi_ket',
        // Item 19: Cacat Tubuh
        'cacat_tubuh',
        // Item 20: Kesimpulan
        'kesimpulan',
        'keterangan_kesimpulan',
        'is_locked',
    ];

    protected $casts = [
        'tgl_periksa' => 'datetime',
        'mata_minus' => 'boolean',
        'mata_minus_nilai' => 'decimal:2',
        'mata_silindris' => 'boolean',
        'mata_silindris_nilai' => 'decimal:2',
        'mata_strabismus' => 'boolean',
        'hidung_cuping' => 'boolean',
        'lidah_stomatitis' => 'boolean',
        'pharing_nyeri_tekan' => 'boolean',
        'tonsil_kemerahan' => 'boolean',
        'tonsil_pembesaran' => 'boolean',
        'gigi_lengkap' => 'boolean',
        'jantung_murmur' => 'boolean',
        'paru_suara_tambahan' => 'boolean',
        'abdomen_hamil' => 'boolean',
        'tulang_skoliosis' => 'boolean',
        'tulang_lordosis' => 'boolean',
        'tulang_kifosis' => 'boolean',
        'tulang_lainnya' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /**
     * Relasi ke tabel mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke tabel users (Dokter)
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    /**
     * Scope untuk data yang sudah terkunci
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope untuk data yang belum terkunci
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Method untuk lock data
     */
    public function lock()
    {
        $this->is_locked = true;
        $this->save();
    }

    /**
     * Method untuk unlock data (hanya superadmin)
     */
    public function unlock()
    {
        $this->is_locked = false;
        $this->save();
    }

    /**
     * Accessor untuk status kesimpulan
     */
    public function getKesimpulanTextAttribute()
    {
        return match($this->kesimpulan) {
            'Memenuhi Syarat' => 'Memenuhi Syarat',
            'Tidak Memenuhi Syarat' => 'Tidak Memenuhi Syarat',
            default => '-'
        };
    }
}
