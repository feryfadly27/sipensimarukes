@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Laporan Pemeriksaan</h1>
            <p class="text-secondary mt-1">Rekap data pemeriksaan kesehatan peserta</p>
        </div>
        <a href="{{ route('laporan.export', request()->query()) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl bg-success text-white font-medium hover:opacity-90 transition-all">
            <i data-lucide="download" class="size-5"></i>
            Unduh Excel
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Total Peserta</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Hadir</p>
            <p class="text-2xl font-bold text-success mt-1">{{ $summary['hadir'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Nakes Selesai</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $summary['plp_selesai'] }}</p>
        </div>
        <div class="rounded-2xl border border-border bg-white p-4">
            <p class="text-sm text-secondary">Dokter Selesai</p>
            <p class="text-2xl font-bold text-foreground mt-1">{{ $summary['dokter_selesai'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-2xl border border-border bg-white p-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari</label>
                  <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / No. Pendaftaran / NIK / No. Telepon"
                       class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Program Studi</label>
                <select name="prodi" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi }}" {{ request('prodi') === $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status Kehadiran</label>
                <select name="status_kehadiran" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua</option>
                    <option value="belum_konfirmasi" {{ request('status_kehadiran') === 'belum_konfirmasi' ? 'selected' : '' }}>Belum Konfirmasi</option>
                    <option value="hadir" {{ request('status_kehadiran') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="tidak_hadir" {{ request('status_kehadiran') === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status Nakes</label>
                <select name="status_plp" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua</option>
                    <option value="belum" {{ request('status_plp') === 'belum' ? 'selected' : '' }}>Belum</option>
                    <option value="selesai" {{ request('status_plp') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status Dokter</label>
                <select name="status_dokter" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua</option>
                    <option value="belum" {{ request('status_dokter') === 'belum' ? 'selected' : '' }}>Belum</option>
                    <option value="selesai" {{ request('status_dokter') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kesimpulan</label>
                <select name="kesimpulan_akhir" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua</option>
                    <option value="memenuhi_syarat" {{ request('kesimpulan_akhir') === 'memenuhi_syarat' ? 'selected' : '' }}>Memenuhi Syarat</option>
                    <option value="tidak_memenuhi_syarat" {{ request('kesimpulan_akhir') === 'tidak_memenuhi_syarat' ? 'selected' : '' }}>Tidak Memenuhi Syarat</option>
                    <option value="-" {{ request('kesimpulan_akhir') === '-' ? 'selected' : '' }}>Belum Ada</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary-hover transition-all">
                    Filter
                </button>
                <a href="{{ route('laporan.index') }}" class="px-6 py-3 rounded-xl bg-muted text-foreground font-medium hover:bg-border transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-border bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted border-b border-border">
                    <tr>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-foreground" style="width: 60px">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">No. Pendaftaran</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Prodi</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Kehadiran</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Nakes</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Dokter</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Kesimpulan</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-foreground">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($mahasiswa as $index => $row)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $mahasiswa->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-sm text-foreground font-medium">{{ $row->no_pendaftaran }}</td>
                            <td class="px-6 py-4 text-sm text-foreground">
                                <div>{{ $row->nama }}</div>
                                <div class="text-xs text-secondary">{{ $row->no_identitas }}</div>
                                <div class="text-xs text-secondary">{{ $row->no_telp ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ $row->prodi }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($row->status_kehadiran === 'hadir') bg-success-light text-success
                                    @elseif($row->status_kehadiran === 'tidak_hadir') bg-error-light text-error
                                    @else bg-warning-light text-warning-dark
                                    @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $row->status_kehadiran)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ ucfirst($row->status_plp) }}</td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ ucfirst($row->status_dokter) }}</td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ ucfirst(str_replace('_', ' ', $row->kesimpulan_akhir)) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('mahasiswa.show', $row) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                                    <i data-lucide="eye" class="size-4"></i>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-secondary">Tidak ada data laporan</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border bg-muted px-6 py-4">
            {{ $mahasiswa->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
