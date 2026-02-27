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
            // Rename prodi_pilihan to prodi_pilihan_1
            $table->renameColumn('prodi_pilihan', 'prodi_pilihan_1');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            // Make prodi_pilihan_1 nullable
            $table->string('prodi_pilihan_1', 100)->nullable()->change();
            
            // Add prodi_pilihan_2
            $table->string('prodi_pilihan_2', 100)->nullable()->after('prodi_pilihan_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn('prodi_pilihan_2');
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->renameColumn('prodi_pilihan_1', 'prodi_pilihan');
            $table->string('prodi_pilihan', 100)->change();
        });
    }
};
