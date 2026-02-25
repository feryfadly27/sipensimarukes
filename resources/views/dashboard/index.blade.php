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

    <!-- 2. Hadir Hari Ini -->
    <div class="flex flex-col rounded-2xl border border-border p-6 gap-3 bg-white hover:ring-1 hover:ring-primary transition-all duration-300">
        <div class="flex items-center gap-[6px]">
            <div class="size-11 bg-success/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="user-check" class="size-6 text-success"></i>
            </div>
            <p class="font-medium text-secondary truncate">Hadir Hari Ini</p>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="font-bold text-[28px] leading-8">{{ $stats['hadir_hari_ini'] }}</p>
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

<!-- Recent Activities -->
<div class="grid grid-cols-1 gap-6">
    <div class="flex flex-col rounded-2xl border border-border p-6 bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-lg text-foreground">Aktivitas Terbaru</h3>
            @if(in_array(auth()->user()->role, ['superadmin', 'admin']))
                <a href="{{ route('log.index') }}" class="cursor-pointer text-sm text-primary font-semibold hover:underline">Lihat Semua</a>
            @endif
        </div>
        
        <div class="flex flex-col">
            @forelse($recent_activities as $log)
                <div class="flex items-start gap-4 py-4 border-b border-border last:border-0">
                    <div class="size-11 rounded-full bg-primary/10 flex items-center justify-center shrink-0 ring-1 ring-border">
                        <span class="text-primary font-bold text-sm">
                            {{ strtoupper(substr($log->user->nama ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-semibold text-foreground text-sm">
                                {{ $log->user->nama ?? 'User' }} 
                                <span class="font-medium text-secondary">{{ $log->aksi }}</span>
                            </p>
                            <span class="text-xs text-secondary shrink-0">
                                {{ $log->waktu->diffForHumans() }}
                            </span>
                        </div>
                        @if($log->target_tabel)
                            <p class="text-sm text-secondary">Target: {{ $log->target_tabel }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                    <p class="text-secondary">Belum ada aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
