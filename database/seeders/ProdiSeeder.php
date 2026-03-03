<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            Prodi::updateOrCreate(
                ['nama' => $nama],
                ['is_active' => true]
            );
        }

        Prodi::whereNotIn('nama', $defaultProdis)->update(['is_active' => false]);

        if (!Schema::hasTable('mahasiswa')) {
            return;
        }

        $legacyValues = collect()
            ->merge(
                Mahasiswa::query()
                    ->whereNotNull('prodi')
                    ->where('prodi', '!=', '')
                    ->distinct()
                    ->pluck('prodi')
            )
            ->merge(
                Mahasiswa::query()
                    ->whereNotNull('prodi_pilihan_1')
                    ->where('prodi_pilihan_1', '!=', '')
                    ->distinct()
                    ->pluck('prodi_pilihan_1')
            )
            ->merge(
                Mahasiswa::query()
                    ->whereNotNull('prodi_pilihan_2')
                    ->where('prodi_pilihan_2', '!=', '')
                    ->distinct()
                    ->pluck('prodi_pilihan_2')
            )
            ->map(fn ($nama) => trim((string) $nama))
            ->filter()
            ->unique();

        foreach ($legacyValues as $nama) {
            Prodi::firstOrCreate(
                ['nama' => $nama],
                ['is_active' => false]
            );
        }
    }
}
