@extends('layouts.app')

@section('title', 'Pemeriksaan Dokter')
@section('page-title', 'Pemeriksaan Dokter')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Form Pemeriksaan Dokter</h2>
            <p class="text-secondary">Lengkapi item pemeriksaan fisik peserta</p>
        </div>
        <a href="{{ route('dokter.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
            <i data-lucide="arrow-left" class="size-4"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <h3 class="text-lg font-semibold text-foreground mb-4">Data Peserta</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-secondary mb-1">No. Pendaftaran</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->no_pendaftaran }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">No. Identitas</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->no_identitas }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">No. Urut</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->nomor_urut ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Nama</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->nama }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Jenis Kelamin</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->jenis_kelamin }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Program Studi</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->prodi ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <h3 class="text-lg font-semibold text-foreground mb-4">Ringkasan Hasil PLP (Read-only)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-secondary mb-1">Riwayat Penyakit</p>
                <p class="font-medium text-foreground">{{ $pemeriksaanPlp->riwayat_penyakit ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Suhu</p>
                <p class="font-medium text-foreground">{{ $pemeriksaanPlp->suhu ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Tensi</p>
                <p class="font-medium text-foreground">{{ $pemeriksaanPlp->tensi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Riwayat Keluarga</p>
                <p class="font-medium text-foreground">{{ $pemeriksaanPlp->riwayat_keluarga ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Buta Warna</p>
                <p class="font-medium text-foreground">{{ $pemeriksaanPlp->buta_warna ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">BMI</p>
                <p class="font-medium text-foreground">{{ $pemeriksaanPlp->bmi ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form id="dokterForm" action="{{ route('dokter.store', $mahasiswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tanggal Pemeriksaan</label>
                <input type="date" name="tgl_periksa" value="{{ old('tgl_periksa', $pemeriksaanDokter?->tgl_periksa?->toDateString() ?? now()->toDateString()) }}" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                @error('tgl_periksa')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kulit</label>
                    <select name="kulit" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                        <option value="">Belum diisi</option>
                        @foreach(['Putih','Kuning','Hitam','Sawo matang'] as $v)
                            <option value="{{ $v }}" @selected(old('kulit') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('kulit')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mata - Kacamata</label>
                    <select name="mata_kacamata" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Berkacamata" @selected(old('mata_kacamata') === 'Berkacamata')>Berkacamata</option>
                        <option value="Tidak berkacamata" @selected(old('mata_kacamata') === 'Tidak berkacamata')>Tidak berkacamata</option>
                    </select>
                    @error('mata_kacamata')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Mata - Normal</label>
                <select name="mata_normal" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                    <option value="">Belum diisi</option>
                    <option value="Normal" @selected(old('mata_normal') === 'Normal')>Normal</option>
                    <option value="Tidak normal" @selected(old('mata_normal') === 'Tidak normal')>Tidak normal</option>
                </select>
                @error('mata_normal')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mata Minus</label>
                    <select name="mata_minus" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('mata_minus', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('mata_minus') === '0')>Tidak</option>
                        <option value="1" @selected(old('mata_minus') === '1')>Ya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nilai Minus</label>
                    <input type="number" step="0.25" name="mata_minus_nilai" value="{{ old('mata_minus_nilai') }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Opsional jika Tidak">
                    @error('mata_minus_nilai')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mata Silindris</label>
                    <select name="mata_silindris" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('mata_silindris', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('mata_silindris') === '0')>Tidak</option>
                        <option value="1" @selected(old('mata_silindris') === '1')>Ya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nilai Silindris</label>
                    <input type="number" step="0.25" name="mata_silindris_nilai" value="{{ old('mata_silindris_nilai') }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Opsional jika Tidak">
                    @error('mata_silindris_nilai')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mata Strabismus</label>
                    <select name="mata_strabismus" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('mata_strabismus', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('mata_strabismus') === '0')>Tidak</option>
                        <option value="1" @selected(old('mata_strabismus') === '1')>Ya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Nilai/Keterangan Strabismus</label>
                <input type="text" name="mata_strabismus_nilai" value="{{ old('mata_strabismus_nilai') }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Opsional jika Tidak">
                @error('mata_strabismus_nilai')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Telinga Kiri</label>
                    <select name="telinga_kiri" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Mendengar jelas" @selected(old('telinga_kiri') === 'Mendengar jelas')>Mendengar jelas</option>
                        <option value="Tidak bisa mendengar" @selected(old('telinga_kiri') === 'Tidak bisa mendengar')>Tidak bisa mendengar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Telinga Kiri</label>
                    <input type="text" name="telinga_kiri_ket" value="{{ old('telinga_kiri_ket') }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Telinga Kanan</label>
                    <select name="telinga_kanan" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Mendengar jelas" @selected(old('telinga_kanan') === 'Mendengar jelas')>Mendengar jelas</option>
                        <option value="Tidak bisa mendengar" @selected(old('telinga_kanan') === 'Tidak bisa mendengar')>Tidak bisa mendengar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Telinga Kanan</label>
                    <input type="text" name="telinga_kanan_ket" value="{{ old('telinga_kanan_ket') }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $yesNoFields = [
                        ['field' => 'hidung_cuping', 'label' => 'Hidung (Cuping)', 'ket' => 'hidung_cuping_ket'],
                        ['field' => 'lidah_stomatitis', 'label' => 'Lidah - Stomatitis', 'ket' => 'lidah_stomatitis_ket'],
                        ['field' => 'pharing_nyeri_tekan', 'label' => 'Pharing - Nyeri Tekan', 'ket' => 'pharing_nyeri_tekan_ket'],
                        ['field' => 'tonsil_kemerahan', 'label' => 'Tonsil - Kemerahan', 'ket' => 'tonsil_kemerahan_ket'],
                        ['field' => 'tonsil_pembesaran', 'label' => 'Tonsil - Pembesaran', 'ket' => null],
                        ['field' => 'gigi_lengkap', 'label' => 'Gigi Lengkap', 'ket' => null],
                        ['field' => 'jantung_murmur', 'label' => 'Jantung - Murmur', 'ket' => 'jantung_murmur_ket'],
                        ['field' => 'paru_suara_tambahan', 'label' => 'Paru - Suara Tambahan', 'ket' => null],
                        ['field' => 'abdomen_hamil', 'label' => 'Abdomen - Hamil', 'ket' => null],
                        ['field' => 'tulang_skoliosis', 'label' => 'Tulang - Skoliosis', 'ket' => 'tulang_skoliosis_ket'],
                        ['field' => 'tulang_lordosis', 'label' => 'Tulang - Lordosis', 'ket' => 'tulang_lordosis_ket'],
                        ['field' => 'tulang_kifosis', 'label' => 'Tulang - Kifosis', 'ket' => 'tulang_kifosis_ket'],
                        ['field' => 'tulang_lainnya', 'label' => 'Tulang - Lainnya', 'ket' => 'tulang_lainnya_ket'],
                    ];
                @endphp

                @foreach($yesNoFields as $item)
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">{{ $item['label'] }}</label>
                        <select name="{{ $item['field'] }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                            <option value="" @selected(old($item['field'], '') === '')>Belum diisi</option>
                            <option value="0" @selected(old($item['field']) === '0')>Tidak</option>
                            <option value="1" @selected(old($item['field']) === '1')>Ya</option>
                        </select>
                        @error($item['field'])<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                        @if($item['ket'])
                            <label class="block text-sm font-medium text-foreground mt-3 mb-2">Keterangan {{ $item['label'] }}</label>
                            <textarea name="{{ $item['ket'] }}" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old($item['ket']) }}</textarea>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Lidah - Kebersihan</label>
                    <select name="lidah_kebersihan" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Bersih" @selected(old('lidah_kebersihan') === 'Bersih')>Bersih</option>
                        <option value="Kurang bersih" @selected(old('lidah_kebersihan') === 'Kurang bersih')>Kurang bersih</option>
                        <option value="Kotor" @selected(old('lidah_kebersihan') === 'Kotor')>Kotor</option>
                    </select>
                    @error('lidah_kebersihan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Lidah - Kebersihan</label>
                    <textarea name="lidah_kebersihan_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('lidah_kebersihan_ket') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Refleks Pupil</label>
                    <select name="pupil" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Isokor" @selected(old('pupil') === 'Isokor')>Isokor</option>
                        <option value="Anisokor" @selected(old('pupil') === 'Anisokor')>Anisokor</option>
                    </select>
                    @error('pupil')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Bicara - Artikulasi</label>
                    <select name="bicara_artikulasi" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Artikulasi jelas" @selected(old('bicara_artikulasi') === 'Artikulasi jelas')>Artikulasi jelas</option>
                        <option value="Tidak jelas" @selected(old('bicara_artikulasi') === 'Tidak jelas')>Tidak jelas</option>
                    </select>
                    @error('bicara_artikulasi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Keterangan Bicara</label>
                <textarea name="bicara_artikulasi_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('bicara_artikulasi_ket') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tiroid</label>
                    <textarea name="tiroid" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('tiroid') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Cacat Tubuh yang Mengganggu Tugas</label>
                    <textarea name="cacat_tubuh" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('cacat_tubuh') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Upload Thorax Photo</label>
                    <input type="file" name="thorax_photo_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full px-4 py-2 border border-border rounded-lg text-sm">
                    @error('thorax_photo_file')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Thorax</label>
                    <textarea name="thorax_photo_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('thorax_photo_ket') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kesimpulan</label>
                    <select name="kesimpulan" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Memenuhi Syarat" @selected(old('kesimpulan') === 'Memenuhi Syarat')>Memenuhi Syarat</option>
                        <option value="Tidak Memenuhi Syarat" @selected(old('kesimpulan') === 'Tidak Memenuhi Syarat')>Tidak Memenuhi Syarat</option>
                    </select>
                    @error('kesimpulan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Kesimpulan</label>
                    <textarea name="keterangan_kesimpulan" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Opsional">{{ old('keterangan_kesimpulan') }}</textarea>
                    @error('keterangan_kesimpulan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('dokter.index') }}" class="px-4 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all">Batal</a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition-all">Simpan & Kunci Pemeriksaan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('dokterForm');
    if (!form) return;

    const requiredControls = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'))
        .filter((el) => !['hidden', 'submit', 'button'].includes(el.type));

    const groups = new Map();

    requiredControls.forEach((control) => {
        const groupName = control.name || control.id;
        if (!groupName) return;

        if (!groups.has(groupName)) {
            groups.set(groupName, []);
        }
        groups.get(groupName).push(control);
    });

    function getLabelForControl(control) {
        if (control.id) {
            const direct = form.querySelector(`label[for="${control.id}"]`);
            if (direct) return direct;
        }

        const container = control.closest('div');
        if (!container) return null;

        const labels = container.querySelectorAll('label');
        return labels.length ? labels[0] : null;
    }

    function ensureBadge(label, groupName) {
        let badge = label.querySelector(`[data-required-status="${groupName}"]`);
        if (badge) return badge;

        label.classList.add('flex', 'items-center', 'justify-between', 'gap-2');

        badge = document.createElement('span');
        badge.dataset.requiredStatus = groupName;
        badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-error-light text-error shrink-0';
        badge.textContent = 'Belum diisi';
        label.appendChild(badge);

        return badge;
    }

    function isControlFilled(control) {
        if (control.type === 'radio' || control.type === 'checkbox') {
            return control.checked;
        }

        if (control.tagName === 'SELECT') {
            return control.value !== '';
        }

        return (control.value || '').trim() !== '';
    }

    function updateGroupStatus(groupName) {
        const controls = groups.get(groupName) || [];
        if (!controls.length) return;

        const label = getLabelForControl(controls[0]);
        if (!label) return;

        const badge = ensureBadge(label, groupName);
        const filled = controls.some((control) => isControlFilled(control));

        if (filled) {
            badge.textContent = 'Sudah terisi';
            badge.classList.remove('bg-error-light', 'text-error');
            badge.classList.add('bg-success-light', 'text-success');
        } else {
            badge.textContent = 'Belum diisi';
            badge.classList.remove('bg-success-light', 'text-success');
            badge.classList.add('bg-error-light', 'text-error');
        }
    }

    groups.forEach((controls, groupName) => {
        controls.forEach((control) => {
            const eventName = (control.type === 'radio' || control.type === 'checkbox' || control.tagName === 'SELECT')
                ? 'change'
                : 'input';

            control.addEventListener(eventName, () => updateGroupStatus(groupName));
        });

        updateGroupStatus(groupName);
    });
});
</script>
@endpush
