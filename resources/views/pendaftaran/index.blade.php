@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2">
        <h1 class="text-3xl font-bold text-foreground">Validasi Kehadiran Peserta</h1>
        <p class="text-secondary">Konfirmasi kehadiran dan berikan nomor urut untuk peserta</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-border p-6 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary font-medium mb-2">Menunggu Validasi</p>
                    <p class="text-4xl font-bold text-foreground">{{ $stats['total_belum_konfirmasi'] }}</p>
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
                    <p class="text-4xl font-bold text-foreground">{{ $stats['total_hadir'] }}</p>
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
                    <p class="text-4xl font-bold text-foreground">{{ $stats['total_tidak_hadir'] }}</p>
                </div>
                <div class="size-14 rounded-2xl bg-red-100 flex items-center justify-center">
                    <i data-lucide="x-circle" class="size-8 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="rounded-2xl border border-border p-6 bg-white">
        <form action="{{ route('pendaftaran.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                <div class="flex items-end gap-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
                        <i data-lucide="filter-x" class="size-4"></i>
                        Filter
                    </button>
                    <a href="{{ route('pendaftaran.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
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
        @if($mahasiswa->hasPages())
            <div class="mt-6 border-t border-border pt-6">
                {{ $mahasiswa->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

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

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
