@extends('layouts.app')

@section('title', 'Data Peserta')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Data Peserta</h1>
            <p class="text-secondary mt-1">Kelola data peserta yang mendaftar</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('mahasiswa.create') }}" class="flex items-center gap-2 px-4 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary-hover transition-all duration-300">
                <i data-lucide="plus" class="size-5"></i>
                Tambah Manual
            </a>
            <button data-modal-id="upload-modal" class="flex items-center gap-2 px-4 py-3 rounded-xl bg-success text-white font-medium hover:opacity-90 transition-all duration-300" onclick="openModal('upload-modal')">
                <i data-lucide="upload" class="size-5"></i>
                Upload Excel
            </button>
        </div>
    </div>

    <!-- Import Errors Alert -->
    @if(session('import_errors'))
        <div class="rounded-xl border border-error/20 bg-error-light p-4">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-circle" class="size-6 text-error shrink-0 mt-1"></i>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-error mb-2">Ada {{ count(session('import_errors')) }} baris yang gagal:</p>
                    <div class="space-y-1 max-h-48 overflow-y-auto text-sm text-error/80">
                        @foreach(session('import_errors') as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="rounded-2xl border border-border bg-white p-6">
        <form method="GET" action="{{ route('mahasiswa.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <!-- Search -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Peserta</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau No. Pendaftaran" 
                       class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>

            <!-- Prodi Filter -->
            <div class="sm:w-48">
                <label class="block text-sm font-medium text-foreground mb-2">Program Studi</label>
                <select name="prodi" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi }}" {{ request('prodi') === $prodi ? 'selected' : '' }}>{{ $prodi }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:w-48">
                <label class="block text-sm font-medium text-foreground mb-2">Status Kehadiran</label>
                <select name="status_kehadiran" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua Status</option>
                    <option value="belum_konfirmasi" {{ request('status_kehadiran') === 'belum_konfirmasi' ? 'selected' : '' }}>Belum Konfirmasi</option>
                    <option value="hadir" {{ request('status_kehadiran') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="tidak_hadir" {{ request('status_kehadiran') === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>
            </div>

            <!-- Per Page -->
            <div class="sm:w-40">
                <label class="block text-sm font-medium text-foreground mb-2">Tampil</label>
                <select name="per_page" class="w-full px-4 py-3 rounded-xl border border-border focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', 25) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary text-white font-medium hover:bg-primary-hover transition-all">
                    Cari
                </button>
                <a href="{{ route('mahasiswa.index') }}" class="px-6 py-3 rounded-xl bg-muted text-foreground font-medium hover:bg-border transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="rounded-2xl border border-border bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted border-b border-border">
                    <tr>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-foreground" style="width: 60px">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">No. Pendaftaran</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Program Studi</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Jenis Kelamin</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Status Kehadiran</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-foreground">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($mahasiswa as $index => $peserta)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4 text-center text-sm text-secondary font-medium">{{ $mahasiswa->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-sm text-foreground font-medium">{{ $peserta->no_pendaftaran }}</td>
                            <td class="px-6 py-4 text-sm text-foreground">
                                <div>{{ $peserta->nama }}</div>
                                <div class="text-xs text-secondary">{{ $peserta->no_identitas }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ $peserta->prodi }}</td>
                            <td class="px-6 py-4 text-sm text-foreground">{{ $peserta->jenis_kelamin }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($peserta->status_kehadiran === 'hadir') bg-success-light text-success
                                    @elseif($peserta->status_kehadiran === 'tidak_hadir') bg-error-light text-error
                                    @else bg-warning-light text-warning-dark
                                    @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $peserta->status_kehadiran)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('mahasiswa.show', $peserta) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                                        <i data-lucide="eye" class="size-4"></i>
                                        Lihat
                                    </a>
                                    <a href="{{ route('mahasiswa.edit', $peserta) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-warning text-white text-sm font-medium hover:bg-warning/90 transition-all">
                                        <i data-lucide="pencil" class="size-4"></i>
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i data-lucide="inbox" class="size-12 text-secondary/30"></i>
                                    <div class="text-secondary">Tidak ada data peserta</div>
                                    <a href="{{ route('mahasiswa.create') }}" class="text-primary font-medium hover:underline">Tambahkan sekarang</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-border bg-muted px-6 py-4">
            {{ $mahasiswa->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('upload-modal')"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md rounded-2xl border border-border bg-white p-6 shadow-2xl">
            <!-- Close Button -->
            <button onclick="closeModal('upload-modal')" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-muted transition-all">
                <i data-lucide="x" class="size-6 text-foreground"></i>
            </button>

            <h2 class="text-2xl font-bold text-foreground mb-2">Upload Data Peserta</h2>
            <p class="text-secondary text-sm mb-6">Unggah file Excel dengan data peserta</p>

            <!-- Upload Form -->
            <form method="POST" action="{{ route('mahasiswa.importExcel') }}" enctype="multipart/form-data">
                @csrf

                <!-- File Input -->
                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-foreground mb-2">Pilih File Excel</label>
                    <div class="border-2 border-dashed border-border rounded-xl p-6 text-center cursor-pointer hover:border-primary transition-all" onclick="document.getElementById('file').click()">
                        <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" required onchange="updateFileName(this)">
                        <i data-lucide="upload-cloud" class="size-8 text-secondary/50 mx-auto mb-2"></i>
                        <p class="text-sm font-medium text-foreground" id="file-name">Klik untuk memilih file</p>
                        <p class="text-xs text-secondary mt-1">Format: .xlsx, .xls, atau .csv (Max 10MB)</p>
                    </div>
                </div>

                <!-- Template Info -->
                <div class="rounded-xl bg-muted p-4 mb-6">
                    <p class="text-sm font-medium text-foreground mb-2">Format Excel:</p>
                    <div class="text-xs text-secondary space-y-1">
                        <p>• Kolom A: No. Pendaftaran *</p>
                        <p>• Kolom B: No. Identitas *</p>
                        <p>• Kolom C: Nama Lengkap *</p>
                        <p>• Kolom D: Tempat Lahir</p>
                        <p>• Kolom E: Tanggal Lahir</p>
                        <p>• Kolom F: Jenis Kelamin</p>
                        <p>• Kolom G: Program Studi</p>
                        <p>• Kolom H: Asal Sekolah</p>
                        <p class="mt-2 text-xs text-secondary/70">* = Wajib diisi</p>
                    </div>
                </div>

                <!-- Download Template -->
                <div class="mb-6">
                    <a href="{{ asset('templates/Template_Data_Peserta.xlsx') }}" download="Template_Data_Peserta.xlsx" class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-primary text-primary font-medium hover:bg-primary/10 transition-all w-full">
                        <i data-lucide="download" class="size-4"></i>
                        Download Template
                    </a>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('upload-modal')" class="flex-1 px-4 py-3 rounded-xl border border-border text-foreground font-medium hover:bg-muted transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-success text-white font-medium hover:opacity-90 transition-all">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Klik untuk memilih file';
    document.getElementById('file-name').textContent = fileName;
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
