@extends('layouts.app')

@section('title', 'Kelola Prodi')
@section('page-title', 'Kelola Prodi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-col gap-2">
            <h1 class="text-3xl font-bold text-foreground">Kelola Prodi</h1>
            <p class="text-secondary">Master data program studi untuk seluruh dropdown pilihan</p>
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('prodis.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Nama Prodi</label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama') }}"
                    placeholder="Contoh: D3 Keperawatan"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    required
                >
                @error('nama')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-secondary">
                    <input type="checkbox" name="is_active" value="1" checked class="size-4 rounded border-border">
                    Aktif
                </label>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                    <i data-lucide="plus" class="size-4"></i>
                    Tambah
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('prodis.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nama prodi"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                    <i data-lucide="filter-x" class="size-4"></i>
                    Filter
                </button>
                <a href="{{ route('prodis.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
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
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Nama Prodi</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Status</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prodis as $index => $prodi)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $prodis->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('prodis.update', $prodi->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input
                                    type="text"
                                    name="nama"
                                    value="{{ $prodi->nama }}"
                                    class="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                                    required
                                >
                                <input type="hidden" name="is_active" value="{{ $prodi->is_active ? 1 : 0 }}">
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-border text-secondary text-sm hover:bg-secondary/10 transition-all">
                                    <i data-lucide="save" class="size-4"></i>
                                    Simpan
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($prodi->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('prodis.toggle', $prodi->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-border text-secondary text-sm hover:bg-secondary/10 transition-all">
                                        <i data-lucide="power" class="size-4"></i>
                                        {{ $prodi->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('prodis.destroy', $prodi->id) }}" method="POST" onsubmit="return confirm('Hapus prodi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-error text-white text-sm hover:bg-error/90 transition-all">
                                        <i data-lucide="trash-2" class="size-4"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Belum ada data prodi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($prodis->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $prodis->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
