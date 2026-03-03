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
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $defaultProdis = [
            'D3 Rekam Medis & Informasi Kesehatan',
            'D3 Rekam Medis & Informasi Kesehatan (Kampus Cirebon)',
            'D3 Gizi',
            'D3 Gizi (Kampus Cirebon)',
            'D3 Keperawatan',
            'D3 Keperawatan (Kampus Cirebon)',
            'D3 Kesehatan Gigi',
            'D3 Farmasi',
            'D3 Kebidanan',
            'D3 Kebidanan (Kampus Cirebon)',
            'D4 Keperawatan',
            'D4 Keperawatan (Kampus Cirebon)',
            'D4 Kebidanan',
            'D4 Kebidanan (Kampus Cirebon)',
            'D4 Terapi Gigi',
            'Pendidikan Profesi Bidan',
            'Pendidikan Profesi Ners',
        ];

        foreach ($defaultProdis as $nama) {
            DB::table('prodis')->insertOrIgnore([
                'nama' => $nama,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('mahasiswa')) {
            $values = collect();

            foreach (['prodi', 'prodi_pilihan_1', 'prodi_pilihan_2'] as $column) {
                if (!Schema::hasColumn('mahasiswa', $column)) {
                    continue;
                }

                $values = $values->merge(
                    DB::table('mahasiswa')
                        ->whereNotNull($column)
                        ->where($column, '!=', '')
                        ->distinct()
                        ->pluck($column)
                );
            }

            foreach ($values->map(fn ($item) => trim((string) $item))->filter()->unique() as $nama) {
                DB::table('prodis')->insertOrIgnore([
                    'nama' => $nama,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
