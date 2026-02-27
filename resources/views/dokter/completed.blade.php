@extends('layouts.app')

@section('title', 'Mahasiswa Sudah Diperiksa')
@section('page-title', 'Mahasiswa Sudah Diperiksa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">List Mahasiswa Sudah Diperiksa</h2>
            <p class="text-secondary">Daftar peserta dengan status pemeriksaan dokter selesai</p>
        </div>
        <a href="{{ route('dokter.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
            <i data-lucide="arrow-left" class="size-4"></i>
            Kembali ke Pemeriksaan
        </a>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('dokter.completed') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari Nama / No. Pendaftaran / No. Identitas</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Ketik pencarian..." 
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Program Studi</label>
                <select name="prodi" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    <option value="">Semua Program</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected($prodi == request('prodi'))>{{ $prodi }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kesimpulan</label>
                <select name="kesimpulan_akhir" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    <option value="">Semua Kesimpulan</option>
                    <option value="memenuhi_syarat" @selected(request('kesimpulan_akhir') === 'memenuhi_syarat')>Memenuhi Syarat</option>
                    <option value="tidak_memenuhi_syarat" @selected(request('kesimpulan_akhir') === 'tidak_memenuhi_syarat')>Tidak Memenuhi Syarat</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tgl Periksa Mulai</label>
                <input 
                    type="date" 
                    name="tgl_periksa_mulai" 
                    value="{{ request('tgl_periksa_mulai') }}" 
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tgl Periksa Selesai</label>
                <input 
                    type="date" 
                    name="tgl_periksa_selesai" 
                    value="{{ request('tgl_periksa_selesai') }}" 
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tampil</label>
                <select name="per_page" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', 25) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                    <i data-lucide="filter-x" class="size-4"></i>
                    Filter
                </button>
                <a href="{{ route('dokter.completed') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                    <i data-lucide="refresh-cw" class="size-4"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm" style="width: 60px">No</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Pendaftaran</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Nama</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Identitas</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Program Studi</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Kesimpulan</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Tgl Periksa</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswa as $index => $row)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $mahasiswa->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-foreground">
                            <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-lg text-xs font-semibold">
                                {{ $row->no_pendaftaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-foreground">{{ $row->nama }}</td>
                        <td class="px-6 py-4 text-sm text-foreground font-mono">{{ $row->no_identitas }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ $row->prodi ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-foreground">{{ $row->kesimpulan_akhir_text }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ optional($row->pemeriksaanDokter?->tgl_periksa)->format('d-m-Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('dokter.show', $row->id) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all">
                                <i data-lucide="eye" class="size-4"></i>
                                Lihat Hasil
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Belum ada mahasiswa yang selesai diperiksa dokter</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($mahasiswa->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $mahasiswa->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
