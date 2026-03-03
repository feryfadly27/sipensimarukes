@extends('layouts.app')

@section('title', 'Tambah Data Peserta')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('mahasiswa.index') }}" class="p-2 rounded-lg hover:bg-muted transition-all">
                <i data-lucide="arrow-left" class="size-6 text-secondary"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-foreground">Tambah Peserta</h1>
                <p class="text-secondary mt-1">Masukkan data peserta baru secara manual</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="rounded-2xl border border-border bg-white p-6">
        <form method="POST" action="{{ route('mahasiswa.store') }}" class="space-y-6">
            @csrf

            <!-- Row 1: No. Pendaftaran & No. Identitas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- No. Pendaftaran -->
                <div>
                    <label for="no_pendaftaran" class="block text-sm font-medium text-foreground mb-2">No. Pendaftaran *</label>
                    <input type="text" id="no_pendaftaran" name="no_pendaftaran" value="{{ old('no_pendaftaran') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('no_pendaftaran') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                           placeholder="Contoh: PEN2026001">
                    @error('no_pendaftaran')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No. Identitas -->
                <div>
                    <label for="no_identitas" class="block text-sm font-medium text-foreground mb-2">No. Identitas / KTP / Paspor *</label>
                    <input type="text" id="no_identitas" name="no_identitas" value="{{ old('no_identitas') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('no_identitas') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                           placeholder="Contoh: 123456789012345">
                    @error('no_identitas')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-foreground mb-2">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('nama') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                       placeholder="Masukkan nama lengkap">
                @error('nama')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Telepon -->
            <div>
                <label for="no_telp" class="block text-sm font-medium text-foreground mb-2">Nomor Telepon</label>
                <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp') }}"
                       inputmode="text" maxlength="12" pattern="(08[0-9]{0,10}|-)"
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('no_telp') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                       placeholder="Contoh: 081234567890 atau - (opsional)">
                @error('no_telp')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Row 2: Tempat Lahir & Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tempat Lahir -->
                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-foreground mb-2">Tempat Lahir *</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('tempat_lahir') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                           placeholder="Contoh: Jakarta">
                    @error('tempat_lahir')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-foreground mb-2">Tanggal Lahir *</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
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
                        <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Program Studi 1 -->
                <div>
                    <label for="prodi_pilihan_1" class="block text-sm font-medium text-foreground mb-2">Program Studi 1 *</label>
                    <select id="prodi_pilihan_1" name="prodi_pilihan_1" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('prodi_pilihan_1') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi }}" {{ old('prodi_pilihan_1') === $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi_pilihan_1')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="prodi_pilihan_2" class="block text-sm font-medium text-foreground mb-2">Program Studi 2</label>
                <select id="prodi_pilihan_2" name="prodi_pilihan_2"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('prodi_pilihan_2') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi }}" {{ old('prodi_pilihan_2') === $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                    @endforeach
                </select>
                @error('prodi_pilihan_2')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Asal Sekolah -->
            <div>
                <label for="asal_sekolah" class="block text-sm font-medium text-foreground mb-2">Asal Sekolah *</label>
                <input type="text" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('asal_sekolah') ? 'border-error' : 'border-border' }} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                       placeholder="Contoh: SMA Negeri 1 Jakarta">
                @error('asal_sekolah')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4 border-t border-border">
                <a href="{{ route('mahasiswa.index') }}" class="flex-1 px-4 py-3 rounded-xl border border-border text-foreground font-medium hover:bg-muted transition-all text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary-hover transition-all">
                    Simpan Peserta
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
