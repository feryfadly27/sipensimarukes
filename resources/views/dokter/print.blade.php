<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil Pemeriksaan - {{ $mahasiswa->nama }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; font-size: 12px; }
        h1, h2 { margin: 0 0 8px; }
        h1 { font-size: 20px; }
        h2 { font-size: 15px; margin-top: 20px; }
        .meta { margin-bottom: 16px; }
        .meta p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #D1D5DB; padding: 6px 8px; vertical-align: top; }
        th { background: #F3F4F6; text-align: left; width: 30%; }
        .actions { margin-top: 20px; }
        .btn { padding: 8px 12px; border: 1px solid #9CA3AF; background: white; cursor: pointer; }
        @media print {
            .actions { display: none; }
            body { margin: 0; }
        }
        .ttd-wrap { margin-top: 28px; width: 100%; }
        .ttd-table { width: 100%; border-collapse: collapse; }
        .ttd-table td { border: none; width: 50%; vertical-align: top; text-align: center; padding: 8px; }
        .ttd-space { height: 60px; }
    </style>
</head>
<body>
@php
    $hasilDokter = $mahasiswa->pemeriksaanDokter;
    $hasilPlp = $mahasiswa->pemeriksaanPlp;
@endphp

    <h1>Laporan Hasil Pemeriksaan Kesehatan</h1>
    <div class="meta">
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y H:i') }}</p>
        <p><strong>No. Pendaftaran:</strong> {{ $mahasiswa->no_pendaftaran }}</p>
        <p><strong>Nama Peserta:</strong> {{ $mahasiswa->nama }}</p>
    </div>

    <h2>Data Peserta</h2>
    <table>
        <tr><th>No. Pendaftaran</th><td>{{ $mahasiswa->no_pendaftaran }}</td></tr>
        <tr><th>No. Identitas</th><td>{{ $mahasiswa->no_identitas }}</td></tr>
        <tr><th>Nama</th><td>{{ $mahasiswa->nama }}</td></tr>
        <tr><th>Jenis Kelamin</th><td>{{ $mahasiswa->jenis_kelamin }}</td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $mahasiswa->tempat_lahir }}, {{ optional($mahasiswa->tanggal_lahir)->format('d-m-Y') }}</td></tr>
        <tr><th>Program Studi</th><td>{{ $mahasiswa->prodi ?? '-' }}</td></tr>
        <tr><th>Asal Sekolah</th><td>{{ $mahasiswa->asal_sekolah ?? '-' }}</td></tr>
        <tr><th>Alamat</th><td>{{ $mahasiswa->alamat ?? '-' }}</td></tr>
        <tr><th>Status Kehadiran</th><td>{{ $mahasiswa->status_kehadiran_text }}</td></tr>
        <tr><th>Status PLP</th><td>{{ ucfirst($mahasiswa->status_plp) }}</td></tr>
        <tr><th>Status Dokter</th><td>{{ ucfirst($mahasiswa->status_dokter) }}</td></tr>
        <tr><th>Kesimpulan Akhir</th><td>{{ $mahasiswa->kesimpulan_akhir_text }}</td></tr>
        <tr><th>Keterangan Kesimpulan</th><td>{{ $mahasiswa->keterangan_kesimpulan ?: '-' }}</td></tr>
    </table>

    <h2>Petugas Pemeriksa</h2>
    <table>
        <tr><th>Petugas Pendaftaran</th><td>{{ $petugasPendaftaran->nama ?? '-' }}</td></tr>
        <tr><th>Petugas PLP</th><td>{{ $hasilPlp->plp->nama ?? '-' }}</td></tr>
        <tr><th>Dokter Pemeriksa</th><td>{{ $hasilDokter->dokter->nama ?? '-' }}</td></tr>
    </table>

    <h2>Hasil Pemeriksaan PLP</h2>
    <table>
        <tr><th>Tanggal Pemeriksaan</th><td>{{ optional($hasilPlp?->tgl_periksa)->format('d-m-Y H:i') ?? '-' }}</td></tr>
        <tr><th>Riwayat Penyakit</th><td>{{ $hasilPlp->riwayat_penyakit ?? '-' }}</td></tr>
        <tr><th>Suhu</th><td>{{ $hasilPlp?->suhu ? $hasilPlp->suhu . ' °C' : '-' }}</td></tr>
        <tr><th>Tensi</th><td>{{ $hasilPlp->tensi ?? '-' }}</td></tr>
        <tr><th>Riwayat Keluarga</th><td>{{ $hasilPlp->riwayat_keluarga ?? '-' }}</td></tr>
        <tr><th>Keterangan Pemeriksaan PLP</th><td>{{ $hasilPlp->keterangan_pemeriksaan ?? '-' }}</td></tr>
        <tr><th>Buta Warna</th><td>{{ $hasilPlp->buta_warna ?? '-' }}</td></tr>
        <tr><th>Tinggi Badan</th><td>{{ $hasilPlp->tinggi_badan ?? '-' }}</td></tr>
        <tr><th>Berat Badan</th><td>{{ $hasilPlp->berat_badan ?? '-' }}</td></tr>
        <tr><th>BMI</th><td>{{ $hasilPlp->bmi ?? '-' }}</td></tr>
    </table>

    <h2>Hasil Pemeriksaan Dokter</h2>
    <table>
        <tr><th>Tanggal Pemeriksaan</th><td>{{ optional($hasilDokter?->tgl_periksa)->format('d-m-Y H:i') ?? '-' }}</td></tr>
        <tr><th>Kulit</th><td>{{ $hasilDokter->kulit ?? '-' }}</td></tr>
        <tr><th>Mata Kacamata</th><td>{{ $hasilDokter->mata_kacamata ?? '-' }}</td></tr>
        <tr><th>Mata Normal</th><td>{{ $hasilDokter->mata_normal ?? '-' }}</td></tr>
        <tr><th>Mata Sklera</th><td>{{ $hasilDokter->mata_sklera ?? '-' }}</td></tr>
        <tr><th>Mata Konjungtiba</th><td>{{ $hasilDokter->mata_konjungtiba ?? '-' }}</td></tr>
        <tr><th>Mata Minus</th><td>{{ $hasilDokter->mata_minus ? 'Ya' : 'Tidak' }} {{ $hasilDokter->mata_minus_nilai ? '(' . $hasilDokter->mata_minus_nilai . ')' : '' }}</td></tr>
        <tr><th>Mata Silindris</th><td>{{ $hasilDokter->mata_silindris ? 'Ya' : 'Tidak' }} {{ $hasilDokter->mata_silindris_nilai ? '(' . $hasilDokter->mata_silindris_nilai . ')' : '' }}</td></tr>
        <tr><th>Mata Strabismus</th><td>{{ $hasilDokter->mata_strabismus ? 'Ya' : 'Tidak' }} {{ $hasilDokter->mata_strabismus_nilai ? '(' . $hasilDokter->mata_strabismus_nilai . ')' : '' }}</td></tr>
        <tr><th>Telinga Kiri</th><td>{{ $hasilDokter->telinga_kiri ?? '-' }}{{ $hasilDokter->telinga_kiri_ket ? ' (' . $hasilDokter->telinga_kiri_ket . ')' : '' }}</td></tr>
        <tr><th>Telinga Kanan</th><td>{{ $hasilDokter->telinga_kanan ?? '-' }}{{ $hasilDokter->telinga_kanan_ket ? ' (' . $hasilDokter->telinga_kanan_ket . ')' : '' }}</td></tr>
        <tr><th>Hidung Cuping</th><td>{{ $hasilDokter->hidung_cuping ? 'Ya' : 'Tidak' }}{{ $hasilDokter->hidung_cuping_ket ? ' (' . $hasilDokter->hidung_cuping_ket . ')' : '' }}</td></tr>
        <tr><th>Lidah Kebersihan</th><td>{{ $hasilDokter->lidah_kebersihan ?? '-' }}{{ $hasilDokter->lidah_kebersihan_ket ? ' (' . $hasilDokter->lidah_kebersihan_ket . ')' : '' }}</td></tr>
        <tr><th>Lidah Stomatitis</th><td>{{ $hasilDokter->lidah_stomatitis ? 'Ya' : 'Tidak' }}{{ $hasilDokter->lidah_stomatitis_ket ? ' (' . $hasilDokter->lidah_stomatitis_ket . ')' : '' }}</td></tr>
        <tr><th>Pharing Nyeri Tekan</th><td>{{ $hasilDokter->pharing_nyeri_tekan ? 'Ya' : 'Tidak' }}{{ $hasilDokter->pharing_nyeri_tekan_ket ? ' (' . $hasilDokter->pharing_nyeri_tekan_ket . ')' : '' }}</td></tr>
        <tr><th>Tonsil Kemerahan</th><td>{{ $hasilDokter->tonsil_kemerahan ? 'Ya' : 'Tidak' }}{{ $hasilDokter->tonsil_kemerahan_ket ? ' (' . $hasilDokter->tonsil_kemerahan_ket . ')' : '' }}</td></tr>
        <tr><th>Tonsil Pembesaran</th><td>{{ $hasilDokter->tonsil_pembesaran ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><th>Gigi Lengkap</th><td>{{ $hasilDokter->gigi_lengkap ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><th>Tiroid</th><td>{{ $hasilDokter->tiroid ?? '-' }}</td></tr>
        <tr><th>Jantung Murmur</th><td>{{ $hasilDokter->jantung_murmur ? 'Ya' : 'Tidak' }}{{ $hasilDokter->jantung_murmur_ket ? ' (' . $hasilDokter->jantung_murmur_ket . ')' : '' }}</td></tr>
        <tr><th>Paru Suara Tambahan</th><td>{{ $hasilDokter->paru_suara_tambahan ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><th>Abdomen Hamil</th><td>{{ $hasilDokter->abdomen_hamil ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><th>Pupil</th><td>{{ $hasilDokter->pupil ?? '-' }}</td></tr>
        <tr><th>Tulang Skoliosis</th><td>{{ $hasilDokter->tulang_skoliosis ? 'Ya' : 'Tidak' }}{{ $hasilDokter->tulang_skoliosis_ket ? ' (' . $hasilDokter->tulang_skoliosis_ket . ')' : '' }}</td></tr>
        <tr><th>Tulang Lordosis</th><td>{{ $hasilDokter->tulang_lordosis ? 'Ya' : 'Tidak' }}{{ $hasilDokter->tulang_lordosis_ket ? ' (' . $hasilDokter->tulang_lordosis_ket . ')' : '' }}</td></tr>
        <tr><th>Tulang Kifosis</th><td>{{ $hasilDokter->tulang_kifosis ? 'Ya' : 'Tidak' }}{{ $hasilDokter->tulang_kifosis_ket ? ' (' . $hasilDokter->tulang_kifosis_ket . ')' : '' }}</td></tr>
        <tr><th>Tulang Lainnya</th><td>{{ $hasilDokter->tulang_lainnya ? 'Ya' : 'Tidak' }}{{ $hasilDokter->tulang_lainnya_ket ? ' (' . $hasilDokter->tulang_lainnya_ket . ')' : '' }}</td></tr>
        <tr><th>Bicara Artikulasi</th><td>{{ $hasilDokter->bicara_artikulasi ?? '-' }}{{ $hasilDokter->bicara_artikulasi_ket ? ' (' . $hasilDokter->bicara_artikulasi_ket . ')' : '' }}</td></tr>
        <tr><th>Cacat Tubuh</th><td>{{ $hasilDokter->cacat_tubuh ?? '-' }}</td></tr>
        <tr><th>Thorax Photo</th><td>{{ $hasilDokter->thorax_photo_file ? \Illuminate\Support\Facades\Storage::url($hasilDokter->thorax_photo_file) : '-' }}</td></tr>
        <tr><th>Keterangan Thorax</th><td>{{ $hasilDokter->thorax_photo_ket ?? '-' }}</td></tr>
        <tr><th>Kesimpulan Dokter</th><td>{{ $hasilDokter->kesimpulan_text }}</td></tr>
        <tr><th>Keterangan Kesimpulan</th><td>{{ $hasilDokter->keterangan_kesimpulan ?? '-' }}</td></tr>
    </table>

    <div class="ttd-wrap">
        <table class="ttd-table">
            <tr>
                <td>
                    <p>Diperiksa oleh PLP,</p>
                    <div class="ttd-space"></div>
                    <p><strong>{{ $hasilPlp->plp->nama ?? '-' }}</strong></p>
                </td>
                <td>
                    <p>Diperiksa oleh Dokter,</p>
                    <div class="ttd-space"></div>
                    <p><strong>{{ $hasilDokter->dokter->nama ?? '-' }}</strong></p>
                </td>
            </tr>
        </table>
    </div>

    <div class="actions">
        <button class="btn" onclick="window.print()">Cetak</button>
        <button class="btn" onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
