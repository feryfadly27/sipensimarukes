@extends('layouts.app')

@section('title', 'Detail Peserta - ' . $mahasiswa->nama)
@section('page-title', 'Detail Peserta')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-foreground">{{ $mahasiswa->nama }}</h1>
            <p class="text-secondary mt-2">{{ $mahasiswa->no_pendaftaran }}</p>
        </div>
        <a href="{{ route('mahasiswa.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-foreground font-medium hover:bg-muted transition-all">
            <i data-lucide="chevron-left" class="size-5"></i>
            Kembali
        </a>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Data Diri -->
        <div class="rounded-2xl border border-border p-6 bg-white">
            <h3 class="font-bold text-lg text-foreground mb-4">Data Diri</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-secondary font-medium">No. Pendaftaran</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->no_pendaftaran }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">No. Identitas</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->no_identitas }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Nama Lengkap</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->nama }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Jenis Kelamin</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->jenis_kelamin }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Akademik -->
        <div class="rounded-2xl border border-border p-6 bg-white">
            <h3 class="font-bold text-lg text-foreground mb-4">Informasi Akademik</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-secondary font-medium">Program Studi</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->prodi }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Prodi Pilihan 1</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->prodi_pilihan_1 ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Prodi Pilihan 2</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->prodi_pilihan_2 ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Asal Sekolah</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->asal_sekolah }}</p>
                </div>
            </div>
        </div>

        <!-- Data Pribadi -->
        <div class="rounded-2xl border border-border p-6 bg-white">
            <h3 class="font-bold text-lg text-foreground mb-4">Data Pribadi</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-secondary font-medium">Tempat Lahir</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->tempat_lahir }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Tanggal Lahir</label>
                    <p class="text-foreground font-medium mt-1">{{ \Carbon\Carbon::parse($mahasiswa->tanggal_lahir)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Alamat</label>
                    <p class="text-foreground font-medium mt-1">{{ $mahasiswa->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Status Pemeriksaan -->
        <div class="rounded-2xl border border-border p-6 bg-white">
            <h3 class="font-bold text-lg text-foreground mb-4">Status Pemeriksaan</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-secondary font-medium">Status Kehadiran</label>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($mahasiswa->status_kehadiran === 'hadir') bg-success-light text-success
                            @elseif($mahasiswa->status_kehadiran === 'tidak_hadir') bg-error-light text-error
                            @else bg-warning-light text-warning-dark
                            @endif
                        ">
                            {{ ucfirst(str_replace('_', ' ', $mahasiswa->status_kehadiran)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Status PLP</label>
                    <p class="text-foreground font-medium mt-1">{{ ucfirst($mahasiswa->status_plp) }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Status Dokter</label>
                    <p class="text-foreground font-medium mt-1">{{ ucfirst($mahasiswa->status_dokter) }}</p>
                </div>
                <div>
                    <label class="text-sm text-secondary font-medium">Kesimpulan Akhir</label>
                    <p class="text-foreground font-medium mt-1">{{ ucfirst(str_replace('_', ' ', $mahasiswa->kesimpulan_akhir)) }}</p>
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        @if($mahasiswa->keterangan_kesimpulan)
            <div class="rounded-2xl border border-border p-6 bg-white md:col-span-2">
                <h3 class="font-bold text-lg text-foreground mb-4">Keterangan</h3>
                <p class="text-foreground">{{ $mahasiswa->keterangan_kesimpulan }}</p>
            </div>
        @endif
    </div>

    <!-- Metadata -->
    <div class="rounded-2xl border border-border p-6 bg-muted/50">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div>
                <label class="text-secondary font-medium">Dibuat Tanggal</label>
                <p class="text-foreground mt-1">{{ $mahasiswa->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <label class="text-secondary font-medium">Terakhir Diperbarui</label>
                <p class="text-foreground mt-1">{{ $mahasiswa->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
