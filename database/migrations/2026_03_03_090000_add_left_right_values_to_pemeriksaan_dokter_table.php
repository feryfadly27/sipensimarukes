<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            if (!Schema::hasColumn('pemeriksaan_dokter', 'mata_minus_nilai_kiri')) {
                $table->decimal('mata_minus_nilai_kiri', 4, 2)->nullable()->after('mata_minus_nilai');
            }

            if (!Schema::hasColumn('pemeriksaan_dokter', 'mata_minus_nilai_kanan')) {
                $table->decimal('mata_minus_nilai_kanan', 4, 2)->nullable()->after('mata_minus_nilai_kiri');
            }

            if (!Schema::hasColumn('pemeriksaan_dokter', 'mata_silindris_nilai_kiri')) {
                $table->decimal('mata_silindris_nilai_kiri', 4, 2)->nullable()->after('mata_silindris_nilai');
            }

            if (!Schema::hasColumn('pemeriksaan_dokter', 'mata_silindris_nilai_kanan')) {
                $table->decimal('mata_silindris_nilai_kanan', 4, 2)->nullable()->after('mata_silindris_nilai_kiri');
            }
        });

        DB::table('pemeriksaan_dokter')
            ->whereNull('mata_minus_nilai_kiri')
            ->update(['mata_minus_nilai_kiri' => DB::raw('mata_minus_nilai')]);

        DB::table('pemeriksaan_dokter')
            ->whereNull('mata_minus_nilai_kanan')
            ->update(['mata_minus_nilai_kanan' => DB::raw('mata_minus_nilai')]);

        DB::table('pemeriksaan_dokter')
            ->whereNull('mata_silindris_nilai_kiri')
            ->update(['mata_silindris_nilai_kiri' => DB::raw('mata_silindris_nilai')]);

        DB::table('pemeriksaan_dokter')
            ->whereNull('mata_silindris_nilai_kanan')
            ->update(['mata_silindris_nilai_kanan' => DB::raw('mata_silindris_nilai')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $dropColumns = [];

            foreach ([
                'mata_minus_nilai_kiri',
                'mata_minus_nilai_kanan',
                'mata_silindris_nilai_kiri',
                'mata_silindris_nilai_kanan',
            ] as $column) {
                if (Schema::hasColumn('pemeriksaan_dokter', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
