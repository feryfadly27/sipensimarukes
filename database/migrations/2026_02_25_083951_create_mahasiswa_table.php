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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran', 30)->unique();
            $table->string('nama', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('prodi_pilihan', 100);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->enum('status_kehadiran', ['belum_hadir', 'hadir', 'tidak_hadir'])->default('belum_hadir');
            $table->enum('status_plp', ['belum', 'selesai'])->default('belum');
            $table->enum('status_dokter', ['belum', 'selesai'])->default('belum');
            $table->enum('kesimpulan_akhir', ['-', 'memenuhi_syarat', 'tidak_memenuhi_syarat'])->default('-');
            $table->text('keterangan_kesimpulan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
