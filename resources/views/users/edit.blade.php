@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Edit User</h1>
            <p class="text-secondary">Perbarui data akun pengguna</p>
        </div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
            <i data-lucide="arrow-left" class="size-4"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                    @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                    @error('username')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                        <option value="superadmin" @selected(old('role', $user->role) === 'superadmin')>Superadmin</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="pendaftaran" @selected(old('role', $user->role) === 'pendaftaran')>Pendaftaran</option>
                        <option value="nakes" @selected(old('role', $user->role) === 'nakes')>Nakes</option>
                        <option value="dokter" @selected(old('role', $user->role) === 'dokter')>Dokter</option>
                    </select>
                    @error('role')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">No. Telp</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    @error('no_telp')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all">Batal</a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
