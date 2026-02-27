@extends('layouts.app')

@section('title', 'Laporan Ringkas')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Laporan Ringkas Keseluruhan</h1>
            <p class="text-secondary mt-1">Ringkasan status peserta dan hasil akhir pemeriksaan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.ringkas.export') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-success text-white font-medium hover:opacity-90 transition-all">
                <i data-lucide="download" class="size-5"></i>
                Unduh Excel Ringkas
            </a>
            <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-muted text-foreground font-medium hover:bg-border transition-all">
                <i data-lucide="arrow-left" class="size-5"></i>
                Kembali ke Laporan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Total Peserta</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $ringkasan['total_peserta'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Hadir Hari Ini</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $ringkasan['hadir_hari_ini'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Hadir Hari Sebelumnya</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $ringkasan['hadir_hari_sebelumnya'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Memenuhi Syarat</p>
            <p class="text-2xl font-bold text-success mt-1">{{ $ringkasan['memenuhi_syarat'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Tidak Memenuhi Syarat</p>
            <p class="text-2xl font-bold text-error mt-1">{{ $ringkasan['tidak_memenuhi_syarat'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-border bg-white overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-success-light/30">
                <h2 class="text-lg font-semibold text-foreground">Mahasiswa Memenuhi Syarat</h2>
                <p class="text-sm text-secondary">Nama dan nomor pendaftaran</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-muted border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-foreground" style="width: 60px">No</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">No. Pendaftaran</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Nama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($memenuhiList as $index => $row)
                            <tr class="hover:bg-muted/50 transition-colors">
                                <td class="px-6 py-3 text-sm text-secondary">{{ $memenuhiList->firstItem() + $index }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-foreground">{{ $row->no_pendaftaran }}</td>
                                <td class="px-6 py-3 text-sm text-foreground">{{ $row->nama }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-secondary">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($memenuhiList->hasPages())
                <div class="px-6 py-4 border-t border-border">{{ $memenuhiList->links('pagination::tailwind') }}</div>
            @endif
        </div>

        <div class="rounded-2xl border border-border bg-white overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-error-light/30">
                <h2 class="text-lg font-semibold text-foreground">Mahasiswa Tidak Memenuhi Syarat</h2>
                <p class="text-sm text-secondary">Nama dan nomor pendaftaran</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-muted border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-foreground" style="width: 60px">No</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">No. Pendaftaran</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Nama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($tidakMemenuhiList as $index => $row)
                            <tr class="hover:bg-muted/50 transition-colors">
                                <td class="px-6 py-3 text-sm text-secondary">{{ $tidakMemenuhiList->firstItem() + $index }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-foreground">{{ $row->no_pendaftaran }}</td>
                                <td class="px-6 py-3 text-sm text-foreground">{{ $row->nama }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-secondary">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tidakMemenuhiList->hasPages())
                <div class="px-6 py-4 border-t border-border">{{ $tidakMemenuhiList->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
