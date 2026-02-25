<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemeriksaan_dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('tgl_periksa')->useCurrent();
            
            // Item 3: Kulit
            $table->enum('kulit', ['Putih', 'Kuning', 'Hitam', 'Sawo matang'])->nullable();
            
            // Item 4: Mata
            $table->enum('mata_kacamata', ['Berkacamata', 'Tidak berkacamata'])->nullable();
            $table->boolean('mata_minus')->default(false);
            $table->decimal('mata_minus_nilai', 4, 2)->nullable();
            $table->boolean('mata_silindris')->default(false);
            $table->decimal('mata_silindris_nilai', 4, 2)->nullable();
            $table->boolean('mata_strabismus')->default(false);
            $table->string('mata_strabismus_nilai', 50)->nullable();
            
            // Item 5: Telinga
            $table->enum('telinga_kiri', ['Mendengar jelas', 'Tidak bisa mendengar'])->nullable();
            $table->text('telinga_kiri_ket')->nullable();
            $table->enum('telinga_kanan', ['Mendengar jelas', 'Tidak bisa mendengar'])->nullable();
            $table->text('telinga_kanan_ket')->nullable();
            
            // Item 6: Hidung (Pernafasan cuping hidung)
            $table->boolean('hidung_cuping')->default(false);
            $table->text('hidung_cuping_ket')->nullable();
            
            // Item 7: Lidah
            $table->enum('lidah_kebersihan', ['Bersih', 'Kurang bersih', 'Kotor'])->nullable();
            $table->text('lidah_kebersihan_ket')->nullable();
            $table->boolean('lidah_stomatitis')->default(false);
            $table->text('lidah_stomatitis_ket')->nullable();
            
            // Item 8: Pharing
            $table->boolean('pharing_nyeri_tekan')->default(false);
            $table->text('pharing_nyeri_tekan_ket')->nullable();
            
            // Item 9: Tonsil
            $table->boolean('tonsil_kemerahan')->default(false);
            $table->text('tonsil_kemerahan_ket')->nullable();
            $table->boolean('tonsil_pembesaran')->default(false);
            
            // Item 10: Gigi
            $table->boolean('gigi_lengkap')->default(true);
            
            // Item 11: Tiroid
            $table->text('tiroid')->nullable();
            
            // Item 12: Jantung
            $table->boolean('jantung_murmur')->default(false);
            $table->text('jantung_murmur_ket')->nullable();
            
            // Item 13: Paru-paru
            $table->boolean('paru_suara_tambahan')->default(false);
            
            // Item 14: Palpasi Abdomen
            $table->boolean('abdomen_hamil')->default(false);
            
            // Item 15: Refleks Pupil
            $table->enum('pupil', ['Isokor', 'Anisokor'])->nullable();
            
            // Item 16: Thorax Photo
            $table->string('thorax_photo_file')->nullable();
            $table->text('thorax_photo_ket')->nullable();
            
            // Item 17: Gangguan Tulang Belakang
            $table->boolean('tulang_skoliosis')->default(false);
            $table->text('tulang_skoliosis_ket')->nullable();
            $table->boolean('tulang_lordosis')->default(false);
            $table->text('tulang_lordosis_ket')->nullable();
            $table->boolean('tulang_kifosis')->default(false);
            $table->text('tulang_kifosis_ket')->nullable();
            $table->boolean('tulang_lainnya')->default(false);
            $table->text('tulang_lainnya_ket')->nullable();
            
            // Item 18: Kemampuan Bicara
            $table->enum('bicara_artikulasi', ['Artikulasi jelas', 'Tidak jelas'])->nullable();
            $table->text('bicara_artikulasi_ket')->nullable();
            
            // Item 19: Cacat Tubuh yang Dapat Mengganggu Tugas
            $table->text('cacat_tubuh')->nullable();
            
            // Item 20: Kesimpulan (Rekomendasi Dokter)
            $table->enum('kesimpulan', ['Memenuhi Syarat', 'Tidak Memenuhi Syarat'])->nullable();
            $table->text('keterangan_kesimpulan')->nullable();
            $table->boolean('is_locked')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_dokter');
    }
};
