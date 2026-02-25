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
        Schema::create('pemeriksaan_plp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('plp_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('tgl_periksa')->useCurrent();
            
            // Item 1: Anamnesa
            $table->text('riwayat_penyakit')->nullable();
            $table->decimal('suhu', 4, 1)->nullable()->comment('Suhu dalam Celcius');
            $table->string('tensi', 20)->nullable()->comment('Format: sistol/diastol mmHg');
            $table->text('riwayat_keluarga')->nullable();
            $table->enum('buta_warna', ['Tidak buta warna', 'Buta warna parsial', 'Buta warna total'])->default('Tidak buta warna');
            
            // Item 2: Antropometri
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('Tinggi badan dalam cm');
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('Berat badan dalam kg');
            $table->decimal('bmi', 5, 2)->nullable()->comment('Body Mass Index');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_plp');
    }
};
