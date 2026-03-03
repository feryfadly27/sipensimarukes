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
            if (!Schema::hasColumn('pemeriksaan_plp', 'catatan_warning_dokter')) {
                $table->text('catatan_warning_dokter')->nullable()->after('keterangan_pemeriksaan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_plp', function (Blueprint $table) {
            if (Schema::hasColumn('pemeriksaan_plp', 'catatan_warning_dokter')) {
                $table->dropColumn('catatan_warning_dokter');
            }
        });
    }
};
