<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pemeriksaan_dokter MODIFY COLUMN status_kelulusan ENUM('Lulus', 'Pending', 'Tidak Lulus', 'Lulus Dengan Syarat') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE pemeriksaan_dokter SET status_kelulusan = 'Lulus Dengan Syarat' WHERE status_kelulusan = 'Pending'");
        DB::statement("ALTER TABLE pemeriksaan_dokter MODIFY COLUMN status_kelulusan ENUM('Lulus', 'Tidak Lulus', 'Lulus Dengan Syarat') NULL");
    }
};
