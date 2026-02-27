@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2">
        <h1 class="text-3xl font-bold text-foreground">Log Aktivitas</h1>
        <p class="text-secondary">Riwayat aktivitas pengguna dalam sistem</p>
    </div>

    <!-- Filter Section -->
    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('logs.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Cari</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari user atau aksi..." 
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    >
                </div>

                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">User</label>
                    <select name="user_id" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                        <option value="">Semua User</option>
                        @foreach($users as $id => $nama)
                            <option value="{{ $id }}" @selected($id == request('user_id'))>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Target Table Filter -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tabel Target</label>
                    <select name="target_tabel" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                        <option value="">Semua Tabel</option>
                        @foreach($tables as $tabel)
                            <option value="{{ $tabel }}" @selected($tabel == request('target_tabel'))>{{ $tabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Dari Tanggal</label>
                    <input 
                        type="date" 
                        name="dari_tanggal" 
                        value="{{ request('dari_tanggal') }}" 
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Sampai Tanggal</label>
                    <input 
                        type="date" 
                        name="sampai_tanggal" 
                        value="{{ request('sampai_tanggal') }}" 
                        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    >
                </div>

                <!-- Action Filter -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Aksi</label>
                    <input 
                        type="text" 
                        name="aksi" 
                        value="{{ request('aksi') }}" 
                        placeholder="Cari aksi..." 
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
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                    <i data-lucide="filter-x" class="size-4"></i>
                    Filter
                </button>
                <a href="{{ route('logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                    <i data-lucide="refresh-cw" class="size-4"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="rounded-2xl border border-border p-6 bg-white overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm" style="width: 60px">No</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">User</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Target</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Waktu</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $logs->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-foreground">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-secondary/10 text-secondary text-xs font-medium">
                                {{ $log->user?->nama ?? 'User Tidak Ketemu' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            <div class="max-w-xs truncate">{{ $log->aksi }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-secondary">
                            @if($log->target_tabel)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-xs font-medium">
                                    {{ $log->target_tabel }}
                                </span>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-secondary">
                            <div class="flex flex-col gap-1">
                                <span>{{ $log->waktu->format('d/m/Y H:i:s') }}</span>
                                <span class="text-xs text-secondary/70">{{ $log->waktu->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-secondary font-mono text-xs">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Belum ada aktivitas</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $logs->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
