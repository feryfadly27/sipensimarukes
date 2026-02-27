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
        Schema::table('pemeriksaan_plp', function (Blueprint $table) {
            // Status pemeriksaan: pending, sedang_diperiksa, selesai
            $table->enum('status_pemeriksaan', ['pending', 'sedang_diperiksa', 'selesai'])->default('pending')->after('plp_id');
            // Waktu pemeriksaan dimulai
            $table->timestamp('started_at')->nullable()->after('status_pemeriksaan');
            // Waktu pemeriksaan selesai
            $table->timestamp('ended_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_plp', function (Blueprint $table) {
            $table->dropColumn(['status_pemeriksaan', 'started_at', 'ended_at']);
        });
    }
};
