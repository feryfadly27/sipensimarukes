<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't enforce decimal precision, no action needed
            return;
        }

        $columns = [
            'mata_minus_nilai',
            'mata_minus_nilai_kiri',
            'mata_minus_nilai_kanan',
            'mata_silindris_nilai',
            'mata_silindris_nilai_kiri',
            'mata_silindris_nilai_kanan',
        ];

        foreach ($columns as $col) {
            if (Schema::hasColumn('pemeriksaan_dokter', $col)) {
                DB::statement("ALTER TABLE pemeriksaan_dokter MODIFY COLUMN {$col} DECIMAL(8,2) NULL");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $columns = [
            'mata_minus_nilai',
            'mata_minus_nilai_kiri',
            'mata_minus_nilai_kanan',
            'mata_silindris_nilai',
            'mata_silindris_nilai_kiri',
            'mata_silindris_nilai_kanan',
        ];

        foreach ($columns as $col) {
            if (Schema::hasColumn('pemeriksaan_dokter', $col)) {
                DB::statement("ALTER TABLE pemeriksaan_dokter MODIFY COLUMN {$col} DECIMAL(4,2) NULL");
            }
        }
    }
};
