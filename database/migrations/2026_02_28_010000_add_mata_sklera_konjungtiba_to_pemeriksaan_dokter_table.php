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
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $table->enum('mata_sklera', ['Normal', 'Tidak normal'])
                ->nullable()
                ->after('mata_normal');
            $table->enum('mata_konjungtiba', ['Normal', 'Tidak normal'])
                ->nullable()
                ->after('mata_sklera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $table->dropColumn(['mata_sklera', 'mata_konjungtiba']);
        });
    }
};
