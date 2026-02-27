<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswa', 'no_identitas')) {
                $table->string('no_identitas', 50)->unique()->after('no_pendaftaran');
            }

            if (!Schema::hasColumn('mahasiswa', 'prodi')) {
                $table->string('prodi', 100)->nullable()->after('prodi_pilihan');
            }

            if (!Schema::hasColumn('mahasiswa', 'asal_sekolah')) {
                $table->string('asal_sekolah', 100)->nullable()->after('prodi');
            }
        });

        // Expand enums to include new values
        DB::statement("ALTER TABLE mahasiswa MODIFY jenis_kelamin ENUM('L','P','Laki-laki','Perempuan')");
        DB::statement("ALTER TABLE mahasiswa MODIFY status_kehadiran ENUM('belum_hadir','belum_konfirmasi','hadir','tidak_hadir') DEFAULT 'belum_konfirmasi'");

        // Normalize existing values
        DB::statement("UPDATE mahasiswa SET jenis_kelamin = CASE WHEN jenis_kelamin = 'L' THEN 'Laki-laki' WHEN jenis_kelamin = 'P' THEN 'Perempuan' ELSE jenis_kelamin END");
        DB::statement("UPDATE mahasiswa SET status_kehadiran = CASE WHEN status_kehadiran = 'belum_hadir' THEN 'belum_konfirmasi' ELSE status_kehadiran END");
        DB::statement("UPDATE mahasiswa SET prodi = prodi_pilihan WHERE prodi IS NULL");

        // Tighten enums to the new set
        DB::statement("ALTER TABLE mahasiswa MODIFY jenis_kelamin ENUM('Laki-laki','Perempuan')");
        DB::statement("ALTER TABLE mahasiswa MODIFY status_kehadiran ENUM('belum_konfirmasi','hadir','tidak_hadir') DEFAULT 'belum_konfirmasi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE mahasiswa MODIFY jenis_kelamin ENUM('L','P')");
        DB::statement("ALTER TABLE mahasiswa MODIFY status_kehadiran ENUM('belum_hadir','hadir','tidak_hadir') DEFAULT 'belum_hadir'");

        Schema::table('mahasiswa', function (Blueprint $table) {
            if (Schema::hasColumn('mahasiswa', 'no_identitas')) {
                $table->dropColumn('no_identitas');
            }

            if (Schema::hasColumn('mahasiswa', 'prodi')) {
                $table->dropColumn('prodi');
            }

            if (Schema::hasColumn('mahasiswa', 'asal_sekolah')) {
                $table->dropColumn('asal_sekolah');
            }
        });
    }
};
