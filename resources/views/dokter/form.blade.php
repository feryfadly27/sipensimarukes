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
        <div class="mt-4 text-sm">
            <p class="text-secondary mb-1">Keterangan Pemeriksaan PLP</p>
            <p class="font-medium text-foreground">{{ $pemeriksaanPlp->keterangan_pemeriksaan ?? '-' }}</p>
        </div>
        <div class="mt-4 text-sm">
            <p class="text-secondary mb-1">Catatan Warning Dokter pada PLP</p>
            <p class="font-medium text-foreground">{{ $pemeriksaanPlp->catatan_warning_dokter ?? '-' }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <form id="dokterForm" action="{{ route('dokter.store', $mahasiswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Catatan Warning Dokter pada PLP</label>
                <textarea
                    name="catatan_warning_dokter"
                    rows="2"
                    maxlength="500"
                    placeholder="Isi warning dokter untuk catatan PLP (opsional)"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >{{ old('catatan_warning_dokter', $pemeriksaanPlp->catatan_warning_dokter ?? '') }}</textarea>
                <p class="text-xs text-secondary mt-1">Jika dikosongkan, akan disimpan sebagai -</p>
                @error('catatan_warning_dokter')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tanggal Pemeriksaan</label>
                <input type="date" name="tgl_periksa" value="{{ old('tgl_periksa', $pemeriksaanDokter?->tgl_periksa?->toDateString() ?? now()->toDateString()) }}" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                @error('tgl_periksa')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mata - Kacamata</label>
                    <select name="mata_kacamata" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Berkacamata" @selected(old('mata_kacamata') === 'Berkacamata')>Berkacamata</option>
                        <option value="Tidak berkacamata" @selected(old('mata_kacamata') === 'Tidak berkacamata')>Tidak berkacamata</option>
                    </select>
                    @error('mata_kacamata')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mata - Ikterik</label>
                    <select name="mata_ikterik" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Tidak" @selected(old('mata_ikterik') === 'Tidak')>Tidak</option>
                        <option value="Ya" @selected(old('mata_ikterik') === 'Ya')>Ya</option>
                    </select>
                    @error('mata_ikterik')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Konjungtiva - Anemis</label>
                    <select name="mata_konjungtiva_anemis" class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Tidak" @selected(old('mata_konjungtiva_anemis') === 'Tidak')>Tidak</option>
                        <option value="Ya" @selected(old('mata_konjungtiva_anemis') === 'Ya')>Ya</option>
                    </select>
                    @error('mata_konjungtiva_anemis')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Pendengaran</label>
                    <select name="pendengaran" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Normal" @selected(old('pendengaran') === 'Normal')>Normal</option>
                        <option value="Terganggu" @selected(old('pendengaran') === 'Terganggu')>Terganggu</option>
                    </select>
                    @error('pendengaran')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Keterangan Pendengaran</label>
                <textarea name="pendengaran_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib diisi jika pendengaran terganggu">{{ old('pendengaran_ket') }}</textarea>
                @error('pendengaran_ket')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
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
                    <label class="block text-sm font-medium text-foreground mb-2">Nilai Minus Kiri</label>
                    <input type="number" step="0.25" min="-9.99" max="9.99" name="mata_minus_nilai_kiri" value="{{ old('mata_minus_nilai_kiri', $pemeriksaanDokter?->mata_minus_nilai_kiri) }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib jika Ya">
                    @error('mata_minus_nilai_kiri')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nilai Minus Kanan</label>
                    <input type="number" step="0.25" min="-9.99" max="9.99" name="mata_minus_nilai_kanan" value="{{ old('mata_minus_nilai_kanan', $pemeriksaanDokter?->mata_minus_nilai_kanan) }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib jika Ya">
                    @error('mata_minus_nilai_kanan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
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
                    <label class="block text-sm font-medium text-foreground mb-2">Nilai Silindris Kiri</label>
                    <input type="number" step="0.25" min="-9.99" max="9.99" name="mata_silindris_nilai_kiri" value="{{ old('mata_silindris_nilai_kiri', $pemeriksaanDokter?->mata_silindris_nilai_kiri) }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib jika Ya">
                    @error('mata_silindris_nilai_kiri')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nilai Silindris Kanan</label>
                    <input type="number" step="0.25" min="-9.99" max="9.99" name="mata_silindris_nilai_kanan" value="{{ old('mata_silindris_nilai_kanan', $pemeriksaanDokter?->mata_silindris_nilai_kanan) }}" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib jika Ya">
                    @error('mata_silindris_nilai_kanan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
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
                    <label class="block text-sm font-medium text-foreground mb-2">Hidung (Cuping)</label>
                    <select name="hidung_cuping" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('hidung_cuping', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('hidung_cuping') === '0')>Tidak</option>
                        <option value="1" @selected(old('hidung_cuping') === '1')>Ya</option>
                    </select>
                    @error('hidung_cuping')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Hidung (Cuping)</label>
                    <textarea name="hidung_cuping_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('hidung_cuping_ket') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mulut - Labioskisis</label>
                    <select name="mulut_labioskisis" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Tidak" @selected(old('mulut_labioskisis') === 'Tidak')>Tidak</option>
                        <option value="Ya" @selected(old('mulut_labioskisis') === 'Ya')>Ya</option>
                    </select>
                    @error('mulut_labioskisis')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mulut - Palatoskisis</label>
                    <select name="mulut_palatoskisis" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Tidak" @selected(old('mulut_palatoskisis') === 'Tidak')>Tidak</option>
                        <option value="Ya" @selected(old('mulut_palatoskisis') === 'Ya')>Ya</option>
                    </select>
                    @error('mulut_palatoskisis')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Pharing - Nyeri Tekan</label>
                    <select name="pharing_nyeri_tekan" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('pharing_nyeri_tekan', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('pharing_nyeri_tekan') === '0')>Tidak</option>
                        <option value="1" @selected(old('pharing_nyeri_tekan') === '1')>Ya</option>
                    </select>
                    @error('pharing_nyeri_tekan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Pharing</label>
                    <textarea name="pharing_nyeri_tekan_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('pharing_nyeri_tekan_ket') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tonsil - Kemerahan</label>
                    <select name="tonsil_kemerahan" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('tonsil_kemerahan', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('tonsil_kemerahan') === '0')>Tidak</option>
                        <option value="1" @selected(old('tonsil_kemerahan') === '1')>Ya</option>
                    </select>
                    @error('tonsil_kemerahan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Tonsil Kemerahan</label>
                    <textarea name="tonsil_kemerahan_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('tonsil_kemerahan_ket') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tonsil - Pembesaran</label>
                    <select name="tonsil_pembesaran" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('tonsil_pembesaran', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('tonsil_pembesaran') === '0')>Tidak</option>
                        <option value="1" @selected(old('tonsil_pembesaran') === '1')>Ya</option>
                    </select>
                    @error('tonsil_pembesaran')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Gigi Lengkap</label>
                    <select name="gigi_lengkap" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('gigi_lengkap', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('gigi_lengkap') === '0')>Tidak</option>
                        <option value="1" @selected(old('gigi_lengkap') === '1')>Ya</option>
                    </select>
                    @error('gigi_lengkap')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Leher - Pembesaran KGB</label>
                    <select name="leher_kgb_pembesaran" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Tidak" @selected(old('leher_kgb_pembesaran') === 'Tidak')>Tidak</option>
                        <option value="Ya" @selected(old('leher_kgb_pembesaran') === 'Ya')>Ya</option>
                    </select>
                    @error('leher_kgb_pembesaran')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Jantung</label>
                    <select name="jantung_dbn" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="DBN" @selected(old('jantung_dbn') === 'DBN')>DBN</option>
                        <option value="Ada Kelainan" @selected(old('jantung_dbn') === 'Ada Kelainan')>Ada Kelainan</option>
                    </select>
                    @error('jantung_dbn')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kelainan Jantung</label>
                    <textarea name="jantung_kelainan" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib diisi jika ada kelainan">{{ old('jantung_kelainan') }}</textarea>
                    @error('jantung_kelainan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Paru</label>
                    <select name="paru_dbn" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="DBN" @selected(old('paru_dbn') === 'DBN')>DBN</option>
                        <option value="Ada Kelainan" @selected(old('paru_dbn') === 'Ada Kelainan')>Ada Kelainan</option>
                    </select>
                    @error('paru_dbn')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kelainan Paru</label>
                    <textarea name="paru_kelainan" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib diisi jika ada kelainan">{{ old('paru_kelainan') }}</textarea>
                    @error('paru_kelainan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Abdomen - Hamil</label>
                    <select name="abdomen_hamil" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="" @selected(old('abdomen_hamil', '') === '')>Belum diisi</option>
                        <option value="0" @selected(old('abdomen_hamil') === '0')>Tidak</option>
                        <option value="1" @selected(old('abdomen_hamil') === '1')>Ya</option>
                    </select>
                    @error('abdomen_hamil')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tulang Belakang</label>
                    <select name="tulang_belakang" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="DBN" @selected(old('tulang_belakang') === 'DBN')>DBN</option>
                        <option value="Lordosis" @selected(old('tulang_belakang') === 'Lordosis')>Lordosis</option>
                        <option value="Kifosis" @selected(old('tulang_belakang') === 'Kifosis')>Kifosis</option>
                        <option value="Skoliosis" @selected(old('tulang_belakang') === 'Skoliosis')>Skoliosis</option>
                    </select>
                    @error('tulang_belakang')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kelengkapan Jari Tangan</label>
                    <select name="jari_tangan_lengkap" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Lengkap" @selected(old('jari_tangan_lengkap') === 'Lengkap')>Lengkap</option>
                        <option value="Tidak Lengkap" @selected(old('jari_tangan_lengkap') === 'Tidak Lengkap')>Tidak Lengkap</option>
                    </select>
                    @error('jari_tangan_lengkap')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Jari Tangan</label>
                    <textarea name="jari_tangan_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib diisi jika tidak lengkap">{{ old('jari_tangan_ket') }}</textarea>
                    @error('jari_tangan_ket')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Bicara - Artikulasi</label>
                    <select name="bicara_artikulasi" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Artikulasi jelas" @selected(old('bicara_artikulasi') === 'Artikulasi jelas')>Artikulasi jelas</option>
                        <option value="Tidak jelas" @selected(old('bicara_artikulasi') === 'Tidak jelas')>Tidak jelas</option>
                    </select>
                    @error('bicara_artikulasi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Cacat Tubuh yang Mengganggu Tugas</label>
                    <textarea name="cacat_tubuh" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('cacat_tubuh') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Keterangan Bicara</label>
                <textarea name="bicara_artikulasi_ket" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm">{{ old('bicara_artikulasi_ket') }}</textarea>
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
                    <label class="block text-sm font-medium text-foreground mb-2">Status Kelulusan</label>
                    <select name="status_kelulusan" class="w-full px-4 py-2 border border-border rounded-lg text-sm" required>
                        <option value="">Belum diisi</option>
                        <option value="Lulus" @selected(old('status_kelulusan', $pemeriksaanDokter?->status_kelulusan) === 'Lulus')>Lulus</option>
                        <option value="Pending" @selected(old('status_kelulusan', $pemeriksaanDokter?->status_kelulusan) === 'Pending')>Pending</option>
                        <option value="Tidak Lulus" @selected(old('status_kelulusan', $pemeriksaanDokter?->status_kelulusan) === 'Tidak Lulus')>Tidak Lulus</option>
                        <option value="Lulus Dengan Syarat" @selected(old('status_kelulusan', $pemeriksaanDokter?->status_kelulusan) === 'Lulus Dengan Syarat')>Lulus Dengan Syarat (Legacy)</option>
                    </select>
                    @error('status_kelulusan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Keterangan Kesimpulan</label>
                    <textarea name="keterangan_kesimpulan" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Wajib untuk Pending / Tidak Lulus">{{ old('keterangan_kesimpulan', $pemeriksaanDokter?->keterangan_kesimpulan) }}</textarea>
                    @error('keterangan_kesimpulan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Surat Rujukan (wajib jika Pending)</label>
                <textarea name="surat_rujukan" rows="2" class="w-full px-4 py-2 border border-border rounded-lg text-sm" placeholder="Contoh: Rujukan ke poli mata RS ...">{{ old('surat_rujukan', $pemeriksaanDokter?->surat_rujukan) }}</textarea>
                <p class="text-xs text-secondary mt-1">Kompatibel data lama: tetap diterima saat status <strong>Lulus Dengan Syarat</strong>.</p>
                @error('surat_rujukan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
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

    const statusKelulusan = form.querySelector('[name="status_kelulusan"]');
    const suratRujukan = form.querySelector('[name="surat_rujukan"]');
    const keteranganKesimpulan = form.querySelector('[name="keterangan_kesimpulan"]');
    const mataMinus = form.querySelector('[name="mata_minus"]');
    const minusKiri = form.querySelector('[name="mata_minus_nilai_kiri"]');
    const minusKanan = form.querySelector('[name="mata_minus_nilai_kanan"]');
    const mataSilindris = form.querySelector('[name="mata_silindris"]');
    const silindrisKiri = form.querySelector('[name="mata_silindris_nilai_kiri"]');
    const silindrisKanan = form.querySelector('[name="mata_silindris_nilai_kanan"]');

    function applyConditionalRequired() {
        if (!statusKelulusan || !suratRujukan || !keteranganKesimpulan) return;

        const status = statusKelulusan.value;
        const requireSurat = status === 'Pending' || status === 'Lulus Dengan Syarat';
        const requireKeterangan = status === 'Pending' || status === 'Tidak Lulus';

        suratRujukan.required = requireSurat;
        keteranganKesimpulan.required = requireKeterangan;
    }

    if (statusKelulusan) {
        statusKelulusan.addEventListener('change', applyConditionalRequired);
        applyConditionalRequired();
    }

    function applyEyeValueRequired() {
        const requireMinusValues = mataMinus && mataMinus.value === '1';
        const requireSilindrisValues = mataSilindris && mataSilindris.value === '1';

        if (minusKiri) minusKiri.required = requireMinusValues;
        if (minusKanan) minusKanan.required = requireMinusValues;
        if (silindrisKiri) silindrisKiri.required = requireSilindrisValues;
        if (silindrisKanan) silindrisKanan.required = requireSilindrisValues;

        if (!requireMinusValues) {
            if (minusKiri) minusKiri.value = '';
            if (minusKanan) minusKanan.value = '';
        }

        if (!requireSilindrisValues) {
            if (silindrisKiri) silindrisKiri.value = '';
            if (silindrisKanan) silindrisKanan.value = '';
        }
    }

    if (mataMinus) {
        mataMinus.addEventListener('change', applyEyeValueRequired);
    }

    if (mataSilindris) {
        mataSilindris.addEventListener('change', applyEyeValueRequired);
    }

    applyEyeValueRequired();

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
