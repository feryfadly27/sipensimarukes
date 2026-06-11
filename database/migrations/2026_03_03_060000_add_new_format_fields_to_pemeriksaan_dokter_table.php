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
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $table->enum('mata_ikterik', ['Tidak', 'Ya'])->nullable()->after('mata_konjungtiba');
            $table->enum('mata_konjungtiva_anemis', ['Tidak', 'Ya'])->nullable()->after('mata_ikterik');
            $table->enum('mulut_labioskisis', ['Tidak', 'Ya'])->nullable()->after('mata_konjungtiva_anemis');
            $table->enum('mulut_palatoskisis', ['Tidak', 'Ya'])->nullable()->after('mulut_labioskisis');
            $table->enum('leher_kgb_pembesaran', ['Tidak', 'Ya'])->nullable()->after('mulut_palatoskisis');
            $table->enum('pendengaran', ['Normal', 'Terganggu'])->nullable()->after('leher_kgb_pembesaran');
            $table->string('pendengaran_ket')->nullable()->after('pendengaran');
            $table->enum('jantung_dbn', ['DBN', 'Ada Kelainan'])->nullable()->after('jantung_murmur_ket');
            $table->string('jantung_kelainan')->nullable()->after('jantung_dbn');
            $table->enum('paru_dbn', ['DBN', 'Ada Kelainan'])->nullable()->after('paru_suara_tambahan');
            $table->string('paru_kelainan')->nullable()->after('paru_dbn');
            $table->enum('tulang_belakang', ['DBN', 'Lordosis', 'Kifosis', 'Skoliosis'])->nullable()->after('thorax_photo_ket');
            $table->enum('jari_tangan_lengkap', ['Lengkap', 'Tidak Lengkap'])->nullable()->after('tulang_belakang');
            $table->string('jari_tangan_ket')->nullable()->after('jari_tangan_lengkap');
            $table->enum('status_kelulusan', ['Lulus', 'Pending', 'Lulus Dengan Syarat', 'Tidak Lulus'])->nullable()->after('jari_tangan_ket');
            $table->text('surat_rujukan')->nullable()->after('status_kelulusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $table->dropColumn([
                'mata_ikterik',
                'mata_konjungtiva_anemis',
                'mulut_labioskisis',
                'mulut_palatoskisis',
                'leher_kgb_pembesaran',
                'pendengaran',
                'pendengaran_ket',
                'jantung_dbn',
                'jantung_kelainan',
                'paru_dbn',
                'paru_kelainan',
                'tulang_belakang',
                'jari_tangan_lengkap',
                'jari_tangan_ket',
                'status_kelulusan',
                'surat_rujukan',
            ]);
        });
    }
};
