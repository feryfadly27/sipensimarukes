@extends('layouts.app')

@section('title', 'Edit Data Peserta')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('mahasiswa.index') }}" class="p-2 rounded-lg hover:bg-muted transition-all">
                <i data-lucide="arrow-left" class="size-6 text-secondary"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-foreground">Edit Peserta</h1>
                <p class="text-secondary mt-1">Ubah data peserta</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="rounded-2xl border border-border bg-white p-6">
        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Row 1: No. Pendaftaran & No. Identitas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- No. Pendaftaran -->
                <div>
                    <label for="no_pendaftaran" class="block text-sm font-medium text-foreground mb-2">No. Pendaftaran *</label>
                    <input type="text" id="no_pendaftaran" name="no_pendaftaran" value="{{ old('no_pendaftaran', $mahasiswa->no_pendaftaran) }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('no_pendaftaran') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('no_pendaftaran')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No. Identitas -->
                <div>
                    <label for="no_identitas" class="block text-sm font-medium text-foreground mb-2">No. Identitas / KTP / Paspor *</label>
                    <input type="text" id="no_identitas" name="no_identitas" value="{{ old('no_identitas', $mahasiswa->no_identitas) }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('no_identitas') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('no_identitas')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-foreground mb-2">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('nama') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                @error('nama')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Row 2: Tempat Lahir & Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tempat Lahir -->
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-foreground mb-2">Tempat Lahir *</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('tempat_lahir') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('tempat_lahir')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-foreground mb-2">Tanggal Lahir *</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir) }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('tanggal_lahir') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('tanggal_lahir')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Row 3: Jenis Kelamin & Program Studi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-foreground mb-2">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('jenis_kelamin') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Program Studi -->
                <div>
                    <label for="prodi" class="block text-sm font-medium text-foreground mb-2">Program Studi *</label>
                    <select id="prodi" name="prodi" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('prodi') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi }}" {{ old('prodi', $mahasiswa->prodi) === $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Asal Sekolah -->
            <div>
                <label for="asal_sekolah" class="block text-sm font-medium text-foreground mb-2">Asal Sekolah *</label>
                <input type="text" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $mahasiswa->asal_sekolah) }}" required
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('asal_sekolah') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                @error('asal_sekolah')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Info -->
            <div class="rounded-xl bg-muted p-4">
                <p class="text-sm text-secondary"><span class="font-medium">Status Kehadiran:</span> {{ ucfirst(str_replace('_', ' ', $mahasiswa->status_kehadiran)) }}</p>
                <p class="text-sm text-secondary mt-1"><span class="font-medium">Dibuat:</span> {{ $mahasiswa->created_at->format('d M Y H:i') }}</p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4 border-t border-border">
                <a href="{{ route('mahasiswa.index') }}" class="flex-1 px-4 py-3 rounded-xl border border-border text-foreground font-medium hover:bg-muted transition-all text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary-hover transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
