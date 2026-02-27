@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Stats Grid (5 Cards) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
    <!-- 1. Total Peserta -->
    <div class="flex flex-col rounded-2xl border border-border p-6 gap-3 bg-white hover:ring-1 hover:ring-primary transition-all duration-300">
        <div class="flex items-center gap-[6px]">
            <div class="size-11 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="users" class="size-6 text-primary"></i>
            </div>
            <p class="font-medium text-secondary truncate">Total Peserta</p>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="font-bold text-[28px] leading-8">{{ $stats['total_peserta'] }}</p>
        </div>
    </div>

    <!-- 2. Total Hadir -->
    <div class="flex flex-col rounded-2xl border border-border p-6 gap-3 bg-white hover:ring-1 hover:ring-primary transition-all duration-300">
        <div class="flex items-center gap-[6px]">
            <div class="size-11 bg-success/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="user-check" class="size-6 text-success"></i>
            </div>
            <p class="font-medium text-secondary truncate">Total Hadir</p>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="font-bold text-[28px] leading-8">{{ $stats['total_hadir'] }}</p>
        </div>
    </div>

    <!-- 3. PLP Selesai -->
    <div class="flex flex-col rounded-2xl border border-border p-6 gap-3 bg-white hover:ring-1 hover:ring-primary transition-all duration-300">
        <div class="flex items-center gap-[6px]">
            <div class="size-11 bg-warning/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="clipboard-check" class="size-6 text-warning-dark"></i>
            </div>
            <p class="font-medium text-secondary truncate">PLP Selesai</p>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="font-bold text-[28px] leading-8">{{ $stats['plp_selesai'] }}</p>
        </div>
    </div>

    <!-- 4. Dokter Selesai -->
    <div class="flex flex-col rounded-2xl border border-border p-6 gap-3 bg-white hover:ring-1 hover:ring-primary transition-all duration-300">
        <div class="flex items-center gap-[6px]">
            <div class="size-11 bg-accent-blue rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="stethoscope" class="size-6 text-primary"></i>
            </div>
            <p class="font-medium text-secondary truncate">Dokter Selesai</p>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="font-bold text-[28px] leading-8">{{ $stats['dokter_selesai'] }}</p>
        </div>
    </div>

    <!-- 5. Memenuhi Syarat -->
    <div class="flex flex-col rounded-2xl border border-border p-6 gap-3 bg-white hover:ring-1 hover:ring-primary transition-all duration-300">
        <div class="flex items-center gap-[6px]">
            <div class="size-11 bg-success/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="badge-check" class="size-6 text-success"></i>
            </div>
            <p class="font-medium text-secondary truncate">Memenuhi Syarat</p>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="font-bold text-[28px] leading-8">{{ $stats['memenuhi_syarat'] }}</p>
        </div>
    </div>
</div>

@if(in_array(auth()->user()->role, ['admin', 'superadmin']))
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="rounded-2xl border border-border p-6 bg-white">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="activity" class="size-5 text-warning-dark"></i>
            <h3 class="font-semibold text-foreground">Sedang Diperiksa PLP</h3>
        </div>
        <div class="space-y-3">
            @forelse(($adminMonitoring['ongoing_plp'] ?? collect()) as $exam)
                <div class="p-3 rounded-lg border border-border bg-warning/5">
                    <p class="text-sm font-semibold text-foreground">{{ $exam->mahasiswa->nama ?? '-' }}</p>
                    <p class="text-xs text-secondary">No. Pendaftaran: {{ $exam->mahasiswa->no_pendaftaran ?? '-' }}</p>
                    <p class="text-xs text-secondary">No. Urut: {{ $exam->mahasiswa->nomor_urut ?? '-' }}</p>
                    <p class="text-xs text-secondary">PLP: {{ $exam->plp->nama ?? '-' }}</p>
                </div>
            @empty
                <p class="text-sm text-secondary">Tidak ada pemeriksaan PLP yang sedang berjalan.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="stethoscope" class="size-5 text-primary"></i>
            <h3 class="font-semibold text-foreground">Dokter Aktif Memeriksa</h3>
        </div>
        <div class="space-y-3">
            @forelse(($adminMonitoring['active_dokter'] ?? collect()) as $active)
                <div class="p-3 rounded-lg border border-border bg-primary/5">
                    <p class="text-sm font-semibold text-foreground">{{ $active['dokter_nama'] ?? '-' }}</p>
                    <p class="text-xs text-secondary">Peserta: {{ $active['mahasiswa_nama'] ?? '-' }}</p>
                    <p class="text-xs text-secondary">No. Pendaftaran: {{ $active['mahasiswa_no_pendaftaran'] ?? '-' }}</p>
                </div>
            @empty
                <p class="text-sm text-secondary">Tidak ada dokter yang sedang aktif memeriksa.</p>
            @endforelse
        </div>
    </div>
</div>
@endif

<!-- Pendaftaran Section (Validasi Kehadiran) -->
@if(auth()->user()->role === 'pendaftaran' || (auth()->user()->role === 'superadmin' && request()->routeIs('pendaftaran.index')))
<div class="space-y-6">
    <!-- Pendaftaran Header -->
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold text-foreground">Validasi Kehadiran Peserta</h2>
        <p class="text-secondary">Konfirmasi kehadiran dan berikan nomor urut untuk peserta</p>
    </div>

    <!-- Pendaftaran Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Menunggu Validasi</p>
                    <p class="text-4xl font-bold text-foreground">{{ $statsPendaftaran['total_belum_konfirmasi'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-yellow-100 flex items-center justify-center">
                    <i data-lucide="clock" class="size-8 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Sudah Hadir</p>
                    <p class="text-4xl font-bold text-foreground">{{ $statsPendaftaran['total_hadir'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-circle" class="size-8 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Tidak Hadir</p>
                    <p class="text-4xl font-bold text-foreground">{{ $statsPendaftaran['total_tidak_hadir'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-red-100 flex items-center justify-center">
                    <i data-lucide="x-circle" class="size-8 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Cari Nama atau No. Pendaftaran</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Ketik nama atau nomor..." 
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    >
                </div>

                <!-- Program Filter -->
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
                    <a href="{{ route('dashboard') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                        <i data-lucide="refresh-cw" class="size-4"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Peserta List -->
    <div class="rounded-2xl border border-border p-6 bg-white overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm" style="width: 60px">No</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Pendaftaran</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Nama</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Identitas</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Program Studi</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswa as $index => $peserta)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $mahasiswa->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-foreground">
                            <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-lg text-xs font-semibold">
                                {{ $peserta->no_pendaftaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold text-foreground">{{ $peserta->nama }}</p>
                                <p class="text-xs text-secondary">{{ $peserta->jenis_kelamin }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground font-mono">{{ $peserta->no_identitas }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ $peserta->prodi ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <button 
                                type="button"
                                onclick="openValidasiModal({{ $peserta->id }}, '{{ $peserta->nama }}')"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all"
                            >
                                <i data-lucide="check" class="size-4"></i>
                                Validasi
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Semua peserta sudah divalidasi ✓</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($mahasiswa && $mahasiswa->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $mahasiswa->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endif

<!-- Dokter Section (Pemeriksaan Dokter) -->
@if(auth()->user()->role === 'dokter' || (auth()->user()->role === 'superadmin' && request()->routeIs('dokter.index')))
<div class="space-y-6">
    <!-- Dokter Header -->
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold text-foreground">Pemeriksaan Dokter</h2>
        <p class="text-secondary">Peserta yang sudah selesai pemeriksaan PLP</p>
    </div>

    <!-- Dokter Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">PLP Selesai</p>
                    <p class="text-4xl font-bold text-foreground">{{ $dokterStats['total_plp_selesai'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="size-8 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Menunggu Dokter</p>
                    <p class="text-4xl font-bold text-foreground">{{ $dokterStats['total_menunggu_dokter'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-yellow-100 flex items-center justify-center">
                    <i data-lucide="clock" class="size-8 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Dokter Selesai</p>
                    <p class="text-4xl font-bold text-foreground">{{ $dokterStats['total_selesai_dokter'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-circle" class="size-8 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Cari Nama atau No. Pendaftaran</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Ketik nama atau nomor..." 
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    >
                </div>

                <!-- Program Filter -->
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
                    <a href="{{ route('dashboard') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                        <i data-lucide="refresh-cw" class="size-4"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Peserta List for Dokter Examination -->
    <div class="rounded-2xl border border-border p-6 bg-white overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm" style="width: 60px">No</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Pendaftaran</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Nama</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Identitas</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Program Studi</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dokterData as $index => $peserta)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $dokterData->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-foreground">
                            <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-lg text-xs font-semibold">
                                {{ $peserta->no_pendaftaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold text-foreground">{{ $peserta->nama }}</p>
                                <p class="text-xs text-secondary">No. Urut: {{ $peserta->nomor_urut }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground font-mono">{{ $peserta->no_identitas }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ $peserta->prodi ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <a
                                href="{{ route('dokter.form', $peserta->id) }}"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all"
                            >
                                <i data-lucide="stethoscope" class="size-4"></i>
                                Periksa
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Belum ada peserta yang selesai PLP ✓</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($dokterData && $dokterData->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $dokterData->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endif

<!-- PLP Section (Pemeriksaan Lab) -->
@if(auth()->user()->role === 'plp' || (auth()->user()->role === 'superadmin' && request()->routeIs('plp.index')))
<div class="space-y-6">
    <!-- PLP Header -->
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold text-foreground">Pemeriksaan Kesehatan Lab (PLP)</h2>
        <p class="text-secondary">Lakukan pemeriksaan kesehatan untuk peserta yang siap diperiksa</p>
    </div>

    <!-- PLP Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Total Hadir</p>
                    <p class="text-4xl font-bold text-foreground">{{ $plpStats['total_hadir'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="size-8 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Menunggu Pemeriksaan</p>
                    <p class="text-4xl font-bold text-foreground">{{ $plpStats['total_menunggu_plp'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-yellow-100 flex items-center justify-center">
                    <i data-lucide="clock" class="size-8 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Pemeriksaan Selesai</p>
                    <p class="text-4xl font-bold text-foreground">{{ $plpStats['total_selesai_plp'] ?? 0 }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-circle" class="size-8 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Cari Nama atau No. Pendaftaran</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Ketik nama atau nomor..." 
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    >
                </div>

                <!-- Program Filter -->
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
                    <a href="{{ route('dashboard') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                        <i data-lucide="refresh-cw" class="size-4"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Next Student & Ongoing Examination Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Peserta Selanjutnya -->
        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="arrow-right" class="size-5 text-primary"></i>
                    <h3 class="font-semibold text-foreground">Peserta Selanjutnya</h3>
                </div>
                <div id="nextStudentInfo" class="p-4 bg-primary/5 rounded-lg space-y-2">
                    <p class="text-sm text-secondary">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Pemeriksaan Sedang Berlangsung -->
        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="activity" class="size-5 text-warning-dark"></i>
                    <h3 class="font-semibold text-foreground">Sedang Diperiksa</h3>
                </div>
                <div id="ongoingExamInfo" class="p-4 bg-warning/5 rounded-lg space-y-2">
                    <p class="text-sm text-secondary">Tidak ada pemeriksaan yang sedang berjalan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Peserta List for Examination -->
    <div class="rounded-2xl border border-border p-6 bg-white overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm" style="width: 60px">No</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Pendaftaran</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Nama</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Identitas</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Program Studi</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plpData as $index => $peserta)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $plpData->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-foreground">
                            <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-lg text-xs font-semibold">
                                {{ $peserta->no_pendaftaran }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold text-foreground">{{ $peserta->nama }}</p>
                                <p class="text-xs text-secondary">No. Urut: {{ $peserta->nomor_urut }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground font-mono">{{ $peserta->no_identitas }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ $peserta->prodi ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <button 
                                type="button"
                                onclick="openPlpModal({{ $peserta->id }}, '{{ $peserta->nama }}')"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all"
                            >
                                <i data-lucide="check" class="size-4"></i>
                                Periksa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Semua peserta sudah selesai diperiksa ✓</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($plpData && $plpData->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $plpData->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endif

<!-- Validasi Modal -->
<div id="validasiModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <!-- Header -->
        <div class="space-y-1 sticky top-0 bg-white pb-4">
            <h2 class="text-xl font-bold text-foreground">Validasi Kehadiran</h2>
            <p class="text-sm text-secondary">
                <span id="pesertaNama" class="font-semibold"></span>
            </p>
        </div>

        <form id="validasiForm" class="space-y-4" enctype="multipart/form-data">
            @csrf
            
            <!-- Status Kehadiran -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-3">Status Kehadiran</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-border rounded-lg hover:bg-secondary/5 transition-all">
                        <input 
                            type="radio" 
                            name="status_kehadiran" 
                            value="hadir"
                            class="size-4"
                            onchange="toggleCameraSection(true)"
                        >
                        <span class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="size-5 text-green-600"></i>
                            <span class="font-medium text-foreground">Hadir</span>
                        </span>
                    </label>
                    
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-border rounded-lg hover:bg-secondary/5 transition-all">
                        <input 
                            type="radio" 
                            name="status_kehadiran" 
                            value="tidak_hadir"
                            class="size-4"
                            onchange="toggleCameraSection(false)"
                        >
                        <span class="flex items-center gap-2">
                            <i data-lucide="x-circle" class="size-5 text-red-600"></i>
                            <span class="font-medium text-foreground">Tidak Hadir</span>
                        </span>
                    </label>
                </div>
                <span class="text-red-500 text-xs mt-1" id="statusError" style="display: none;"></span>
            </div>

            <!-- Camera Section (shown only when Hadir is selected) -->
            <div id="cameraSection" style="display: none;" class="space-y-3">
                <p class="text-xs text-secondary">Ambil foto kehadiran (opsional)</p>
                
                <!-- Camera Tabs -->
                <div class="flex gap-2 border-b border-border">
                    <button type="button" onclick="switchCameraMode('capture')" id="captureTab" class="flex-1 py-2 text-sm font-medium border-b-2 border-primary text-primary">
                        Ambil Foto
                    </button>
                    <button type="button" onclick="switchCameraMode('upload')" id="uploadTab" class="flex-1 py-2 text-sm font-medium border-b-2 border-transparent text-secondary hover:text-foreground">
                        Unggah File
                    </button>
                </div>

                <!-- Capture Mode -->
                <div id="captureMode">
                    <div class="space-y-2">
                        <video id="videoStream" class="w-full rounded-lg bg-black/10 hidden"></video>
                        <canvas id="photoCanvas" class="w-full rounded-lg bg-black/10 hidden"></canvas>
                        <img id="photoPreview" class="w-full rounded-lg hidden" />
                        
                        <div id="cameraInitSection" class="space-y-2">
                            <button type="button" onclick="initializeCamera()" class="w-full px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                                <i data-lucide="camera" class="size-4 inline mr-2"></i>
                                Buka Kamera
                            </button>
                        </div>
                        
                        <div id="cameraActiveSection" style="display: none;" class="space-y-2">
                            <button type="button" onclick="capturePhoto()" class="w-full px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                                <i data-lucide="camera" class="size-4 inline mr-2"></i>
                                Ambil Foto
                            </button>
                            <button type="button" onclick="stopCamera()" class="w-full px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                                <i data-lucide="x" class="size-4 inline mr-2"></i>
                                Tutup Kamera
                            </button>
                        </div>

                        <div id="photoTakenSection" style="display: none;" class="space-y-2">
                            <button type="button" onclick="capturePhoto()" class="w-full px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                                <i data-lucide="repeat" class="size-4 inline mr-2"></i>
                                Ambil Ulang
                            </button>
                            <button type="button" onclick="stopCamera()" class="w-full px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                                <i data-lucide="x" class="size-4 inline mr-2"></i>
                                Tutup Kamera
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload Mode -->
                <div id="uploadMode" style="display: none;">
                    <input 
                        type="file" 
                        id="fotoInput" 
                        name="foto"
                        accept="image/jpeg,image/png,image/jpg"
                        class="hidden"
                        onchange="handleFileSelect(event)"
                    >
                    <label for="fotoInput" class="w-full px-4 py-3 rounded-lg border-2 border-dashed border-border hover:border-primary cursor-pointer text-center transition-all">
                        <i data-lucide="upload" class="size-6 text-secondary mx-auto mb-2"></i>
                        <p class="text-sm font-medium text-foreground">Klik untuk memilih file</p>
                        <p class="text-xs text-secondary">PNG, JPG, JPEG (Max 5MB)</p>
                    </label>
                    <div id="uploadPreviewContainer" style="display: none;" class="mt-2 space-y-2">
                        <img id="uploadPreview" class="w-full rounded-lg" />
                        <button type="button" onclick="clearUploadFile()" class="w-full px-3 py-2 rounded-lg border border-border text-secondary text-xs font-medium hover:bg-secondary/10 transition-all">
                            <i data-lucide="x" class="size-3 inline mr-1"></i>
                            Hapus File
                        </button>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4 border-t border-border sticky bottom-0 bg-white">
                <button 
                    type="button" 
                    onclick="closeValidasiModal()"
                    class="flex-1 px-4 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition-all"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- PLP Verification Modal -->
<div id="plpVerificationModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <!-- Header -->
        <div class="space-y-1 sticky top-0 bg-white pb-4">
            <h2 class="text-xl font-bold text-foreground">Verifikasi Data Mahasiswa</h2>
            <p class="text-sm text-secondary">Periksa data dan foto sebelum memulai pemeriksaan</p>
        </div>

        <!-- Student Data Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Photo Section -->
            <div class="space-y-3">
                <p class="text-sm font-medium text-foreground">Foto Kehadiran</p>
                <div id="verificationPhotoContainer" class="rounded-lg overflow-hidden bg-secondary/10 border border-border" style="height: 300px;">
                    <div id="verificationPhotoLoading" class="w-full h-full flex items-center justify-center">
                        <div class="text-center">
                            <i data-lucide="image" class="size-12 text-secondary mx-auto mb-2"></i>
                            <p class="text-sm text-secondary">Memuat foto...</p>
                        </div>
                    </div>
                    <img id="verificationPhoto" style="display: none;" class="w-full h-full object-cover">
                    <div id="verificationNoPhoto" style="display: none;" class="w-full h-full flex items-center justify-center">
                        <div class="text-center">
                            <i data-lucide="camera-off" class="size-12 text-secondary mx-auto mb-2"></i>
                            <p class="text-sm text-secondary">Tidak ada foto kehadiran</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Section -->
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-secondary font-medium mb-1">Nama Lengkap</p>
                    <p id="verifyNama" class="font-semibold text-foreground">-</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-secondary font-medium mb-1">No. Pendaftaran</p>
                        <p id="verifyNoPendaftaran" class="text-sm font-mono text-foreground">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary font-medium mb-1">No. Urut</p>
                        <p id="verifyNomorUrut" class="text-sm font-semibold text-primary text-lg">-</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-secondary font-medium mb-1">No. Identitas</p>
                    <p id="verifyNoIdentitas" class="text-sm font-mono text-foreground">-</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-secondary font-medium mb-1">Jenis Kelamin</p>
                        <p id="verifyJenisKelamin" class="text-sm text-foreground">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary font-medium mb-1">Program Studi 1</p>
                        <p id="verifyProdi1" class="text-sm text-foreground">-</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-secondary font-medium mb-1">Program Studi 2</p>
                    <p id="verifyProdi2" class="text-sm text-foreground">-</p>
                </div>

                <div>
                    <p class="text-xs text-secondary font-medium mb-1">Tempat/Tanggal Lahir</p>
                    <p id="verifyTanggalLahir" class="text-sm text-foreground">-</p>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="p-4 rounded-lg border border-success bg-success/5 space-y-2">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="size-5 text-success"></i>
                <p class="font-medium text-foreground">Data Terverifikasi</p>
            </div>
            <p class="text-sm text-secondary">Silakan lanjutkan untuk memulai pemeriksaan kesehatan lab</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-border sticky bottom-0 bg-white">
            <button 
                type="button" 
                onclick="closePlpVerificationModal()"
                class="flex-1 px-4 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all"
            >
                Batal
            </button>
            <button 
                type="button"
                onclick="proceedToExamination()"
                class="flex-1 px-4 py-2 rounded-lg bg-success text-white font-medium hover:bg-success/90 transition-all inline-flex items-center justify-center gap-2"
            >
                <i data-lucide="arrow-right" class="size-4"></i>
                Lanjutkan Pemeriksaan
            </button>
        </div>
    </div>
</div>

<!-- PLP Modal -->
<div id="plpModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 space-y-4">
        <!-- Header -->
        <div class="space-y-1 sticky top-0 bg-white pb-4">
            <h2 class="text-xl font-bold text-foreground">Pemeriksaan Kesehatan Lab (PLP)</h2>
            <p class="text-sm text-secondary">
                <span id="plpPesertaNama" class="font-semibold"></span>
            </p>
        </div>

        <form id="plpForm" class="space-y-4" enctype="multipart/form-data">
            @csrf
            
            <!-- Tanggal Periksa -->
            <div>
                <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                    <span>Tanggal Pemeriksaan</span>
                    <span id="status-tgl_periksa" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                </label>
                <input 
                    type="date" 
                    name="tgl_periksa" 
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    required
                >
                <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
            </div>

            <!-- Riwayat Penyakit -->
            <div>
                <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                    <span>Riwayat Penyakit</span>
                    <span id="status-riwayat_penyakit" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                </label>
                <textarea 
                    name="riwayat_penyakit" 
                    placeholder="Cth: Diabates, Hipertensi, dll..."
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    rows="2"
                    required
                ></textarea>
                <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
            </div>

            <!-- Suhu -->
            <div>
                <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                    <span>Suhu (°C)</span>
                    <span id="status-suhu" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                </label>
                <input 
                    type="number" 
                    name="suhu" 
                    step="0.1"
                    min="0"
                    max="42"
                    placeholder="36.5"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    required
                >
                <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
            </div>

            <!-- Tensi -->
            <div>
                <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                    <span>Tekanan Darah (mmHg)</span>
                    <span id="status-tensi" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                </label>
                <input 
                    type="text" 
                    name="tensi" 
                    placeholder="120/80"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    required
                >
                <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
            </div>

            <!-- Riwayat Keluarga -->
            <div>
                <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                    <span>Riwayat Penyakit Keluarga</span>
                    <span id="status-riwayat_keluarga" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                </label>
                <textarea 
                    name="riwayat_keluarga" 
                    placeholder="Cth: Hipertensi (Ayah), dll..."
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    rows="2"
                    required
                ></textarea>
                <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
            </div>

            <!-- Buta Warna -->
            <div>
                <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                    <span>Tes Buta Warna</span>
                    <span id="status-buta_warna" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                </label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-border rounded-lg hover:bg-secondary/5 transition-all">
                        <input 
                            type="radio" 
                            name="buta_warna" 
                            value="Tidak buta warna"
                            class="size-4"
                            required
                        >
                        <span class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="size-5 text-green-600"></i>
                            <span class="font-medium text-foreground">Normal</span>
                        </span>
                    </label>
                    
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-border rounded-lg hover:bg-secondary/5 transition-all">
                        <input 
                            type="radio" 
                            name="buta_warna" 
                            value="Buta warna parsial"
                            class="size-4"
                            required
                        >
                        <span class="flex items-center gap-2">
                            <i data-lucide="x-circle" class="size-5 text-red-600"></i>
                            <span class="font-medium text-foreground">Abnormal</span>
                        </span>
                    </label>
                </div>
                <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
            </div>

            <!-- Physical Measurements (Grid) -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Tinggi Badan -->
                <div>
                    <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                        <span>Tinggi Badan (cm)</span>
                        <span id="status-tinggi_badan" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                    </label>
                    <input 
                        type="number" 
                        name="tinggi_badan" 
                        step="0.1"
                        min="0"
                        max="250"
                        placeholder="170"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                        required
                    >
                    <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
                </div>

                <!-- Berat Badan -->
                <div>
                    <label class="flex items-center justify-between text-sm font-medium text-foreground mb-2">
                        <span>Berat Badan (kg)</span>
                        <span id="status-berat_badan" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error">Belum diisi</span>
                    </label>
                    <input 
                        type="number" 
                        name="berat_badan" 
                        step="0.1"
                        min="0"
                        max="200"
                        placeholder="70"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                        required
                    >
                    <span class="text-red-500 text-xs mt-1" style="display: none;"></span>
                </div>
            </div>

            <!-- BMI (Read-only) -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">BMI (Indeks Massa Tubuh)</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        id="bmiValue"
                        class="flex-1 px-4 py-2 border border-border rounded-lg bg-secondary/5 text-sm"
                        readonly
                        placeholder="Hitung BMI"
                    >
                    <button 
                        type="button"
                        onclick="calculateBmi()"
                        class="px-4 py-2 rounded-lg bg-secondary text-white text-sm font-medium hover:bg-secondary/90 transition-all"
                    >
                        Hitung
                    </button>
                </div>
                <p id="bmiStatus" class="text-xs text-secondary mt-2">Status: -</p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4 border-t border-border sticky bottom-0 bg-white">
                <button 
                    type="button" 
                    onclick="closePlpModal()"
                    class="flex-1 px-4 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition-all"
                >
                    Simpan Pemeriksaan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentPesertaId = null;
    let videoStream = null;
    let selectedPhotoBlob = null;
    let cameraMode = 'capture';

    function openValidasiModal(pesertaId, pesertaNama) {
        currentPesertaId = pesertaId;
        document.getElementById('pesertaNama').textContent = pesertaNama;
        document.getElementById('validasiForm').reset();
        document.getElementById('cameraSection').style.display = 'none';
        document.getElementById('validasiModal').classList.remove('hidden');
        resetCameraUI();
        document.addEventListener('keydown', handleEscapeKey);
        lucide.createIcons();
    }

    function closeValidasiModal() {
        stopCamera();
        selectedPhotoBlob = null;
        clearUploadFile();
        document.getElementById('validasiModal').classList.add('hidden');
        document.removeEventListener('keydown', handleEscapeKey);
    }

    function handleEscapeKey(e) {
        if (e.key === 'Escape') {
            closeValidasiModal();
        }
    }

    function toggleCameraSection(isHadir) {
        const cameraSection = document.getElementById('cameraSection');
        if (isHadir) {
            cameraSection.style.display = 'block';
        } else {
            cameraSection.style.display = 'none';
            stopCamera();
            clearUploadFile();
        }
        clearErrors();
    }

    function switchCameraMode(mode) {
        cameraMode = mode;
        document.getElementById('captureTab').classList.toggle('border-primary', mode === 'capture');
        document.getElementById('captureTab').classList.toggle('text-primary', mode === 'capture');
        document.getElementById('captureTab').classList.toggle('border-transparent', mode !== 'capture');
        document.getElementById('captureTab').classList.toggle('text-secondary', mode !== 'capture');
        
        document.getElementById('uploadTab').classList.toggle('border-primary', mode === 'upload');
        document.getElementById('uploadTab').classList.toggle('text-primary', mode === 'upload');
        document.getElementById('uploadTab').classList.toggle('border-transparent', mode !== 'upload');
        document.getElementById('uploadTab').classList.toggle('text-secondary', mode !== 'upload');
        
        document.getElementById('captureMode').style.display = mode === 'capture' ? 'block' : 'none';
        document.getElementById('uploadMode').style.display = mode === 'upload' ? 'block' : 'none';
        
        if (mode === 'capture') {
            resetCameraUI();
        }
    }

    async function initializeCamera() {
        try {
            const constraints = { 
                video: { 
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            };
            
            videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            const video = document.getElementById('videoStream');
            video.srcObject = videoStream;
            video.style.display = 'block';
            
            document.getElementById('cameraInitSection').style.display = 'none';
            document.getElementById('cameraActiveSection').style.display = 'block';
            
            // Play video
            video.play();
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        }
    }

    function capturePhoto() {
        const video = document.getElementById('videoStream');
        const canvas = document.getElementById('photoCanvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw current video frame to canvas
        ctx.drawImage(video, 0, 0);
        
        // Show preview and hide video
        canvas.style.display = 'block';
        video.style.display = 'none';
        document.getElementById('cameraActiveSection').style.display = 'none';
        document.getElementById('photoTakenSection').style.display = 'block';
        
        // Store blob for upload
        canvas.toBlob(function(blob) {
            selectedPhotoBlob = blob;
        }, 'image/jpeg', 0.9);
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        
        document.getElementById('videoStream').style.display = 'none';
        document.getElementById('photoCanvas').style.display = 'none';
        document.getElementById('cameraActiveSection').style.display = 'none';
        document.getElementById('photoTakenSection').style.display = 'none';
        document.getElementById('cameraInitSection').style.display = 'block';
    }

    function resetCameraUI() {
        stopCamera();
        selectedPhotoBlob = null;
        document.getElementById('videoStream').style.display = 'none';
        document.getElementById('photoCanvas').style.display = 'none';
        document.getElementById('cameraInitSection').style.display = 'block';
        document.getElementById('cameraActiveSection').style.display = 'none';
        document.getElementById('photoTakenSection').style.display = 'none';
    }

    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('uploadPreview').src = event.target.result;
                document.getElementById('uploadPreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearUploadFile() {
        document.getElementById('fotoInput').value = '';
        document.getElementById('uploadPreviewContainer').style.display = 'none';
    }

    function clearErrors() {
        document.getElementById('statusError').style.display = 'none';
    }

    document.getElementById('validasiForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const statusKehadiran = document.querySelector('input[name="status_kehadiran"]:checked');
        
        if (!statusKehadiran) {
            document.getElementById('statusError').textContent = 'Pilih status kehadiran terlebih dahulu';
            document.getElementById('statusError').style.display = 'block';
            return;
        }

        // Create FormData for handling both file and regular data
        const formData = new FormData();
        formData.append('status_kehadiran', statusKehadiran.value);
        
        // Add photo if available
        if (selectedPhotoBlob) {
            formData.append('foto', selectedPhotoBlob, 'photo.jpg');
        } else if (cameraMode === 'upload' && document.getElementById('fotoInput').files.length > 0) {
            formData.append('foto', document.getElementById('fotoInput').files[0]);
        }

        try {
            const response = await fetch(`/pendaftaran/${currentPesertaId}/validasi`, {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.message) {
                    document.getElementById('statusError').textContent = result.message;
                    document.getElementById('statusError').style.display = 'block';
                }
                return;
            }

            // Success - reload page
            setTimeout(() => {
                location.reload();
            }, 500);

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('statusError').textContent = 'Terjadi kesalahan, silakan coba lagi';
            document.getElementById('statusError').style.display = 'block';
        }
    });

    // Close modal when clicking outside
    document.getElementById('validasiModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeValidasiModal();
        }
    });

    document.getElementById('plpVerificationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePlpVerificationModal();
        }
    });

    document.getElementById('plpModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePlpModal();
        }
    });

    document.getElementById('plpModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePlpModal();
        }
    });

    // PLP Modal Functions
    async function openPlpModal(pesertaId, pesertaNama) {
        currentPesertaId = pesertaId;
        
        try {
            // First: Verify student data
            const verifyResponse = await fetch(`/plp/${pesertaId}/verify`, {
                method: 'GET',
                headers: {
                    'X-CSRF-Token': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json'
                }
            });

            const verifyResult = await verifyResponse.json();

            if (!verifyResponse.ok) {
                alert(verifyResult.message || 'Gagal memverifikasi data');
                return;
            }

            // Show verification modal with student data
            displayStudentVerification(verifyResult.data);
            document.getElementById('plpVerificationModal').classList.remove('hidden');
            lucide.createIcons();
            
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan, silakan coba lagi');
        }
    }

    function displayStudentVerification(data) {
        // Fill student data
        document.getElementById('verifyNama').textContent = data.nama;
        document.getElementById('verifyNoPendaftaran').textContent = data.no_pendaftaran;
        document.getElementById('verifyNoIdentitas').textContent = data.no_identitas;
        document.getElementById('verifyNomorUrut').textContent = data.nomor_urut || '-';
        document.getElementById('verifyJenisKelamin').textContent = data.jenis_kelamin;
        document.getElementById('verifyProdi1').textContent = data.prodi_pilihan_1 || data.prodi || '-';
        document.getElementById('verifyProdi2').textContent = data.prodi_pilihan_2 || '-';
        
        const tanggalLahir = data.tempat_lahir + ', ' + data.tanggal_lahir;
        document.getElementById('verifyTanggalLahir').textContent = tanggalLahir;

        // Handle photo
        const photoContainer = document.getElementById('verificationPhotoContainer');
        const photoLoading = document.getElementById('verificationPhotoLoading');
        const photoImg = document.getElementById('verificationPhoto');
        const noPhoto = document.getElementById('verificationNoPhoto');

        if (data.foto_kehadiran) {
            photoImg.src = data.foto_kehadiran;
            photoImg.style.display = 'block';
            photoLoading.style.display = 'none';
            noPhoto.style.display = 'none';
        } else {
            photoLoading.style.display = 'none';
            photoImg.style.display = 'none';
            noPhoto.style.display = 'flex';
        }
    }

    async function proceedToExamination() {
        try {
            // Start examination (mark as sedang_diperiksa)
            const response = await fetch(`/plp/${currentPesertaId}/start`, {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                alert(result.message || 'Gagal memulai pemeriksaan');
                return;
            }

            // Close verification modal and show exam form
            closePlpVerificationModal();
            document.getElementById('plpPesertaNama').textContent = result.data.mahasiswa?.nama || 'Mahasiswa';
            document.getElementById('plpForm').reset();
            setPlpDefaultValues();
            document.getElementById('plpModal').classList.remove('hidden');
            document.addEventListener('keydown', handlePlpEscapeKey);
            lucide.createIcons();
            
            // Refresh student info
            loadNextStudentInfo();
            loadOngoingExamInfo();
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan, silakan coba lagi');
        }
    }

    function closePlpVerificationModal() {
        document.getElementById('plpVerificationModal').classList.add('hidden');
    }

    function closePlpModal() {
        document.getElementById('plpModal').classList.add('hidden');
        document.getElementById('plpForm').reset();
        setPlpDefaultValues();
        document.removeEventListener('keydown', handlePlpEscapeKey);
    }

    function handlePlpEscapeKey(e) {
        if (e.key === 'Escape') {
            closePlpModal();
        }
    }

    function calculateBmi() {
        const tinggi = parseFloat(document.querySelector('input[name="tinggi_badan"]').value);
        const berat = parseFloat(document.querySelector('input[name="berat_badan"]').value);

        if (!tinggi || !berat) {
            alert('Masukkan tinggi dan berat badan terlebih dahulu');
            return;
        }

        const tinggiMeter = tinggi / 100;
        const bmi = (berat / (tinggiMeter * tinggiMeter)).toFixed(2);
        document.getElementById('bmiValue').value = bmi;
        document.getElementById('bmiStatus').textContent = `Status: ${getBmiStatus(parseFloat(bmi))}`;
    }

    function getBmiStatus(bmi) {
        if (bmi < 18.5) return 'Kurus';
        if (bmi < 25) return 'Normal';
        if (bmi < 30) return 'Overweight';
        return 'Obesitas';
    }

    function setPlpDefaultValues() {
        const form = document.getElementById('plpForm');
        form.querySelector('input[name="tgl_periksa"]').value = '';
        form.querySelector('textarea[name="riwayat_penyakit"]').value = '';
        form.querySelector('input[name="suhu"]').value = '';
        form.querySelector('input[name="tensi"]').value = '';
        form.querySelector('textarea[name="riwayat_keluarga"]').value = '';
        form.querySelectorAll('input[name="buta_warna"]').forEach((radio) => {
            radio.checked = false;
        });
        form.querySelector('input[name="tinggi_badan"]').value = '';
        form.querySelector('input[name="berat_badan"]').value = '';
        document.getElementById('bmiValue').value = '';
        document.getElementById('bmiStatus').textContent = 'Status: -';
        updatePlpFieldStatuses();
    }

    function setPlpFieldStatus(fieldName, isFilled) {
        const statusEl = document.getElementById(`status-${fieldName}`);
        if (!statusEl) {
            return;
        }

        if (isFilled) {
            statusEl.textContent = 'Sudah terisi';
            statusEl.classList.remove('bg-error-light', 'text-error');
            statusEl.classList.add('bg-success-light', 'text-success');
        } else {
            statusEl.textContent = 'Belum diisi';
            statusEl.classList.remove('bg-success-light', 'text-success');
            statusEl.classList.add('bg-error-light', 'text-error');
        }
    }

    function updatePlpFieldStatuses() {
        const form = document.getElementById('plpForm');
        if (!form) {
            return;
        }

        const tglPeriksa = form.querySelector('input[name="tgl_periksa"]')?.value?.trim();
        const riwayatPenyakit = form.querySelector('textarea[name="riwayat_penyakit"]')?.value?.trim();
        const suhu = parseFloat(form.querySelector('input[name="suhu"]')?.value || '0');
        const tensi = form.querySelector('input[name="tensi"]')?.value?.trim();
        const riwayatKeluarga = form.querySelector('textarea[name="riwayat_keluarga"]')?.value?.trim();
        const butaWarna = form.querySelector('input[name="buta_warna"]:checked')?.value;
        const tinggiBadan = parseFloat(form.querySelector('input[name="tinggi_badan"]')?.value || '0');
        const beratBadan = parseFloat(form.querySelector('input[name="berat_badan"]')?.value || '0');

        setPlpFieldStatus('tgl_periksa', !!tglPeriksa);
        setPlpFieldStatus('riwayat_penyakit', !!riwayatPenyakit);
        setPlpFieldStatus('suhu', !Number.isNaN(suhu) && suhu > 0);
        setPlpFieldStatus('tensi', !!tensi);
        setPlpFieldStatus('riwayat_keluarga', !!riwayatKeluarga);
        setPlpFieldStatus('buta_warna', !!butaWarna);
        setPlpFieldStatus('tinggi_badan', !Number.isNaN(tinggiBadan) && tinggiBadan > 0);
        setPlpFieldStatus('berat_badan', !Number.isNaN(beratBadan) && beratBadan > 0);
    }

    function bindPlpStatusListeners() {
        const form = document.getElementById('plpForm');
        if (!form) {
            return;
        }

        const selectors = [
            'input[name="tgl_periksa"]',
            'textarea[name="riwayat_penyakit"]',
            'input[name="suhu"]',
            'input[name="tensi"]',
            'textarea[name="riwayat_keluarga"]',
            'input[name="buta_warna"]',
            'input[name="tinggi_badan"]',
            'input[name="berat_badan"]',
        ];

        selectors.forEach((selector) => {
            form.querySelectorAll(selector).forEach((el) => {
                const eventName = el.type === 'radio' ? 'change' : 'input';
                el.addEventListener(eventName, updatePlpFieldStatuses);
            });
        });

        updatePlpFieldStatuses();
    }

    document.getElementById('plpForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!this.reportValidity()) {
            return;
        }

        if (!confirm('Pastikan data sudah benar dan siap disimpan. Lanjutkan?')) {
            return;
        }

        const formData = new FormData(this);

        try {
            const response = await fetch(`/plp/${currentPesertaId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.message) {
                    alert(result.message);
                } else if (result.errors) {
                    let errorMessages = 'Validasi gagal:\n';
                    for (const [field, errors] of Object.entries(result.errors)) {
                        errorMessages += `${field}: ${errors.join(', ')}\n`;
                    }
                    alert(errorMessages);
                }
                return;
            }

            alert(result.message || 'Pemeriksaan berhasil disimpan');
            setTimeout(() => {
                location.reload();
            }, 200);

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan, silakan coba lagi');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        bindPlpStatusListeners();
        loadNextStudentInfo();
        loadOngoingExamInfo();
        
        // Refresh info every 5 seconds
        setInterval(() => {
            loadNextStudentInfo();
            loadOngoingExamInfo();
        }, 5000);
    });

    // Load next student to be examined
    async function loadNextStudentInfo() {
        try {
            const response = await fetch('/dashboard');
            // Get next student from current visible list
            const tableRows = document.querySelectorAll('tbody tr');
            if (tableRows.length > 0) {
                const firstRow = tableRows[0];
                const nama = firstRow.querySelector('td:nth-child(3)')?.innerText || 'Data tidak tersedia';
                const noUrut = firstRow.querySelector('td:nth-child(3) .text-xs')?.innerText || '-';
                
                document.getElementById('nextStudentInfo').innerHTML = `
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary mb-1">Nama</p>
                            <p class="font-semibold text-foreground">${nama.split('\n')[0]}</p>
                            <p class="text-xs text-secondary mt-2">${noUrut}</p>
                        </div>
                        <span class="inline-block bg-primary text-white px-3 py-1 rounded text-xs font-semibold">Siap</span>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading next student:', error);
        }
    }

    // Load ongoing examination info
    async function loadOngoingExamInfo() {
        try {
            const response = await fetch('/plp/check-ongoing');
            const result = await response.json();
            
            const ongoingDiv = document.getElementById('ongoingExamInfo');
            if (result.has_ongoing) {
                let ongoingHtml = '<div class="space-y-3">';
                result.data.forEach((exam, index) => {
                    ongoingHtml += `
                        <div class="flex items-start justify-between p-3 bg-warning/5 rounded-lg border border-warning/20">
                            <div>
                                <p class="text-xs text-secondary mb-1">PLP: ${exam.plp_nama || '-'} - ${exam.mahasiswa_nama}</p>
                                <p class="text-xs text-warning-dark">No. Urut: ${exam.mahasiswa_no_urut}</p>
                                <p class="text-xs text-secondary mt-1">Mulai: ${new Date(exam.started_at).toLocaleTimeString('id-ID')}</p>
                            </div>
                            <span class="inline-flex text-xs font-semibold text-warning-dark bg-warning/20 px-2 py-1 rounded">
                                <i data-lucide="dot" class="size-3 animate-pulse mr-1"></i>
                                Berlangsung
                            </span>
                        </div>
                    `;
                });
                ongoingHtml += '</div>';
                ongoingDiv.innerHTML = ongoingHtml;
            } else {
                ongoingDiv.innerHTML = '<p class="text-sm text-secondary"><i data-lucide="check-circle" class="text-success size-4 inline mr-1"></i>Tidak ada pemeriksaan yang sedang berjalan</p>';
            }
        } catch (error) {
            console.error('Error loading ongoing exam:', error);
        }
    }
</script>
@endpush
@endsection
