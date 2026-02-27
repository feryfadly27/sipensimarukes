# Changelog

Semua perubahan penting pada proyek ini didokumentasikan di file ini.

## [2026-02-27]

### Added
- Fitur pemeriksaan dokter end-to-end:
  - Route dan controller pemeriksaan dokter.
  - Form input pemeriksaan item 3–20.
  - Penyimpanan hasil pemeriksaan, lock data (`is_locked`), dan update status mahasiswa.
- Halaman daftar mahasiswa yang sudah diperiksa dokter (riwayat selesai).
- Filter tambahan pada daftar selesai dokter:
  - pencarian,
  - program studi,
  - kesimpulan,
  - rentang tanggal pemeriksaan.
- Menu superadmin diperluas agar dapat mengakses alur operasional lain (pendaftaran, PLP, dokter) melalui mode halaman terkait.
- Modul `Kelola User` khusus superadmin:
  - list user,
  - tambah user,
  - edit user,
  - hapus user,
  - filter dan pagination.
- Halaman `Laporan Ringkas` keseluruhan berisi:
  - total peserta,
  - hadir hari ini,
  - hadir hari sebelumnya,
  - jumlah memenuhi syarat,
  - jumlah tidak memenuhi syarat,
  - daftar mahasiswa memenuhi syarat,
  - daftar mahasiswa tidak memenuhi syarat.
- Tombol unduh `Excel Ringkas` untuk halaman laporan ringkas dengan format rapi.

### Changed
- Metrik dashboard diubah dari `Hadir Hari Ini` menjadi akumulatif `Total Hadir`.
- Penamaan key statistik diselaraskan dari `hadir_hari_ini` menjadi `total_hadir`.
- Form pemeriksaan dokter dirapikan:
  - field status dan keterangan dibuat berdekatan,
  - pilihan default dibuat `Belum diisi` untuk mengurangi salah input real-time.
- Semua tabel yang menggunakan pagination kini mendukung pilihan jumlah data:
  - `10`, `25`, `50`, `100`.
- Sinkronisasi status pada laporan (UI dan export) agar mengikuti data pemeriksaan aktual, sehingga mengurangi mismatch status `belum/selesai`.

### Fixed
- Ketidaksesuaian status pada laporan unduhan (kasus data sudah diperiksa namun laporan masih menampilkan belum).
- Menu sidebar `Kelola User` yang sebelumnya mengarah ke placeholder kini mengarah ke halaman manajemen user yang benar.

---

### Catatan Rilis
- Perubahan difokuskan pada peningkatan akurasi laporan, kelengkapan alur dokter, dan perluasan hak akses superadmin secara terkontrol.
