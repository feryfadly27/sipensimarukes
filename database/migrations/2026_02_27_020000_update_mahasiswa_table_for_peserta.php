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

        // Normalize existing values (compatible with SQLite and MySQL)
        DB::statement("UPDATE mahasiswa SET jenis_kelamin = CASE WHEN jenis_kelamin = 'L' THEN 'Laki-laki' WHEN jenis_kelamin = 'P' THEN 'Perempuan' ELSE jenis_kelamin END");
        DB::statement("UPDATE mahasiswa SET prodi = prodi_pilihan WHERE prodi IS NULL");

        // MySQL-only: modify enum columns (skip on SQLite)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE mahasiswa MODIFY jenis_kelamin ENUM('Laki-laki','Perempuan')");
            DB::statement("ALTER TABLE mahasiswa MODIFY status_kehadiran ENUM('belum_hadir','hadir','tidak_hadir') DEFAULT 'belum_hadir'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE mahasiswa MODIFY jenis_kelamin ENUM('L','P')");
            DB::statement("ALTER TABLE mahasiswa MODIFY status_kehadiran ENUM('belum_hadir','hadir','tidak_hadir') DEFAULT 'belum_hadir'");
        }

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
