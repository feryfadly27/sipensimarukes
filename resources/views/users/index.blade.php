@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-col gap-2">
            <h1 class="text-3xl font-bold text-foreground">Kelola User</h1>
            <p class="text-secondary">Daftar akun pengguna sistem</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
            <i data-lucide="plus" class="size-4"></i>
            Tambah User
        </a>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Nama / Username / No. Telp"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Role</label>
                <select name="role" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    <option value="">Semua Role</option>
                    <option value="superadmin" @selected(request('role') === 'superadmin')>Superadmin</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="pendaftaran" @selected(request('role') === 'pendaftaran')>Pendaftaran</option>
                    <option value="plp" @selected(request('role') === 'plp')>PLP</option>
                    <option value="dokter" @selected(request('role') === 'dokter')>Dokter</option>
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
                <a href="{{ route('users.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
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
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm" style="width:60px">No</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Nama</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Username</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Role</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">No. Telp</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Dibuat</th>
                    <th class="text-left px-6 py-4 font-semibold text-foreground text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr class="border-b border-border hover:bg-secondary/5 transition-all">
                        <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $users->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-foreground">{{ $user->nama }}</td>
                        <td class="px-6 py-4 text-sm text-foreground">{{ $user->username }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ ucfirst($user->role) }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ $user->no_telp ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ optional($user->created_at)->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
                                    <i data-lucide="pencil" class="size-4"></i>
                                    Edit
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Hapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-error text-white text-sm font-medium hover:bg-error/90 transition-all">
                                            <i data-lucide="trash-2" class="size-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="size-12 text-secondary mx-auto mb-3"></i>
                            <p class="text-secondary">Belum ada data user</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $users->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
