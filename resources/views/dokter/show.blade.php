@extends('layouts.app')

@section('title', 'Hasil Pemeriksaan Dokter - ' . $mahasiswa->nama)
@section('page-title', 'Hasil Pemeriksaan Dokter')

@section('content')
@php
    $hasil = $mahasiswa->pemeriksaanDokter;
    $hasilPlp = $mahasiswa->pemeriksaanPlp;
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Hasil Pemeriksaan Dokter</h2>
            <p class="text-secondary">Detail hasil pemeriksaan peserta yang sudah selesai</p>
        </div>
        <a href="{{ route('dokter.completed') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-secondary text-sm font-medium hover:bg-secondary/10 transition-all">
            <i data-lucide="arrow-left" class="size-4"></i>
            Kembali
        </a>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('dokter.print', $mahasiswa->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all">
            <i data-lucide="printer" class="size-4"></i>
            Cetak Hasil Lengkap
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
                <p class="text-secondary mb-1">Nama</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->nama }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Program Studi</p>
                <p class="font-semibold text-foreground">{{ $mahasiswa->prodi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Tanggal Pemeriksaan</p>
                <p class="font-semibold text-foreground">{{ optional($hasil->tgl_periksa)->format('d-m-Y H:i') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Dokter Pemeriksa</p>
                <p class="font-semibold text-foreground">{{ $hasil->dokter->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Petugas PLP Pemeriksa</p>
                <p class="font-semibold text-foreground">{{ $hasilPlp->plp->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Petugas Pendaftaran</p>
                <p class="font-semibold text-foreground">{{ $petugasPendaftaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Tanggal Pemeriksaan PLP</p>
                <p class="font-semibold text-foreground">{{ optional($hasilPlp?->tgl_periksa)->format('d-m-Y H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white">
        <h3 class="text-lg font-semibold text-foreground mb-4">Ringkasan Hasil PLP</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-secondary mb-1">Suhu</p>
                <p class="font-semibold text-foreground">{{ $hasilPlp?->suhu ? $hasilPlp->suhu . ' °C' : '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">Tensi</p>
                <p class="font-semibold text-foreground">{{ $hasilPlp?->tensi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-secondary mb-1">BMI</p>
                <p class="font-semibold text-foreground">{{ $hasilPlp?->bmi ?? '-' }}</p>
            </div>
        </div>
        <div class="mt-4 text-sm">
            <p class="text-secondary mb-1">Keterangan Pemeriksaan PLP</p>
            <p class="font-semibold text-foreground">{{ $hasilPlp?->keterangan_pemeriksaan ?? '-' }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-border p-6 bg-white overflow-x-auto">
        <h3 class="text-lg font-semibold text-foreground mb-4">Ringkasan Hasil</h3>
        <table class="w-full text-sm">
            <tbody>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary w-1/3">Kesimpulan</td>
                    <td class="py-3 font-semibold text-foreground">{{ $hasil->kesimpulan_text }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Keterangan Kesimpulan</td>
                    <td class="py-3 text-foreground">{{ $hasil->keterangan_kesimpulan ?: '-' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Kulit</td>
                    <td class="py-3 text-foreground">{{ $hasil->kulit ?: '-' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Mata</td>
                    <td class="py-3 text-foreground">{{ $hasil->mata_kacamata ?: '-' }} | Normal: {{ $hasil->mata_normal ?: '-' }} | Sklera: {{ $hasil->mata_sklera ?: '-' }} | Konjungtiba: {{ $hasil->mata_konjungtiba ?: '-' }} | Minus: {{ $hasil->mata_minus ? 'Ya' : 'Tidak' }} {{ $hasil->mata_minus_nilai ? '(' . $hasil->mata_minus_nilai . ')' : '' }} | Silindris: {{ $hasil->mata_silindris ? 'Ya' : 'Tidak' }} {{ $hasil->mata_silindris_nilai ? '(' . $hasil->mata_silindris_nilai . ')' : '' }} | Strabismus: {{ $hasil->mata_strabismus ? 'Ya' : 'Tidak' }} {{ $hasil->mata_strabismus_nilai ? '(' . $hasil->mata_strabismus_nilai . ')' : '' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Telinga Kiri / Kanan</td>
                    <td class="py-3 text-foreground">{{ $hasil->telinga_kiri ?: '-' }}{{ $hasil->telinga_kiri_ket ? ' (' . $hasil->telinga_kiri_ket . ')' : '' }} | {{ $hasil->telinga_kanan ?: '-' }}{{ $hasil->telinga_kanan_ket ? ' (' . $hasil->telinga_kanan_ket . ')' : '' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Lidah</td>
                    <td class="py-3 text-foreground">Kebersihan: {{ $hasil->lidah_kebersihan ?: '-' }}{{ $hasil->lidah_kebersihan_ket ? ' (' . $hasil->lidah_kebersihan_ket . ')' : '' }} | Stomatitis: {{ $hasil->lidah_stomatitis ? 'Ya' : 'Tidak' }}{{ $hasil->lidah_stomatitis_ket ? ' (' . $hasil->lidah_stomatitis_ket . ')' : '' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Pharing & Tonsil</td>
                    <td class="py-3 text-foreground">Pharing nyeri tekan: {{ $hasil->pharing_nyeri_tekan ? 'Ya' : 'Tidak' }}{{ $hasil->pharing_nyeri_tekan_ket ? ' (' . $hasil->pharing_nyeri_tekan_ket . ')' : '' }} | Tonsil kemerahan: {{ $hasil->tonsil_kemerahan ? 'Ya' : 'Tidak' }}{{ $hasil->tonsil_kemerahan_ket ? ' (' . $hasil->tonsil_kemerahan_ket . ')' : '' }} | Tonsil pembesaran: {{ $hasil->tonsil_pembesaran ? 'Ya' : 'Tidak' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Jantung & Paru</td>
                    <td class="py-3 text-foreground">Murmur: {{ $hasil->jantung_murmur ? 'Ya' : 'Tidak' }}{{ $hasil->jantung_murmur_ket ? ' (' . $hasil->jantung_murmur_ket . ')' : '' }} | Suara tambahan paru: {{ $hasil->paru_suara_tambahan ? 'Ya' : 'Tidak' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Pupil & Bicara</td>
                    <td class="py-3 text-foreground">Pupil: {{ $hasil->pupil ?: '-' }} | Bicara: {{ $hasil->bicara_artikulasi ?: '-' }}{{ $hasil->bicara_artikulasi_ket ? ' (' . $hasil->bicara_artikulasi_ket . ')' : '' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Tulang Belakang</td>
                    <td class="py-3 text-foreground">Skoliosis: {{ $hasil->tulang_skoliosis ? 'Ya' : 'Tidak' }}{{ $hasil->tulang_skoliosis_ket ? ' (' . $hasil->tulang_skoliosis_ket . ')' : '' }} | Lordosis: {{ $hasil->tulang_lordosis ? 'Ya' : 'Tidak' }}{{ $hasil->tulang_lordosis_ket ? ' (' . $hasil->tulang_lordosis_ket . ')' : '' }} | Kifosis: {{ $hasil->tulang_kifosis ? 'Ya' : 'Tidak' }}{{ $hasil->tulang_kifosis_ket ? ' (' . $hasil->tulang_kifosis_ket . ')' : '' }} | Lainnya: {{ $hasil->tulang_lainnya ? 'Ya' : 'Tidak' }}{{ $hasil->tulang_lainnya_ket ? ' (' . $hasil->tulang_lainnya_ket . ')' : '' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-3 text-secondary">Cacat Tubuh</td>
                    <td class="py-3 text-foreground">{{ $hasil->cacat_tubuh ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="py-3 text-secondary">Thorax Photo</td>
                    <td class="py-3 text-foreground">
                        @if($hasil->thorax_photo_file)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($hasil->thorax_photo_file) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-border text-secondary font-medium hover:bg-secondary/10 transition-all">
                                <i data-lucide="file-text" class="size-4"></i>
                                Lihat File
                            </a>
                            @if($hasil->thorax_photo_ket)
                                <p class="text-xs text-secondary mt-2">{{ $hasil->thorax_photo_ket }}</p>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
