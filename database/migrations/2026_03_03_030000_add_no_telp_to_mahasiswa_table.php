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
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswa', 'no_telp')) {
                $table->string('no_telp', 20)->nullable()->after('no_identitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (Schema::hasColumn('mahasiswa', 'no_telp')) {
                $table->dropColumn('no_telp');
            }
        });
    }
};
