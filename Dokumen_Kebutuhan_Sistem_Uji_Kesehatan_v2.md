# DOKUMEN KEBUTUHAN PENGEMBANGAN SISTEM
# Uji Kesehatan Sipenmaru Poltekkes Kemenkes
# Versi 2.0 | Februari 2026
# ============================================================

---

## A. IDENTITAS DOKUMEN

| Item | Keterangan |
|---|---|
| Nama Sistem | Sistem Informasi Uji Kesehatan Sipenmaru |
| Versi Dokumen | 2.0 |
| Tanggal | Februari 2026 |
| Status | Draft Revisi |

---

## B. LATAR BELAKANG

Sistem ini dikembangkan untuk mendukung proses uji kesehatan calon mahasiswa baru
Poltekkes Kemenkes dalam rangka Sipenmaru jalur PMDP. Proses berjalan secara
bertahap dan saling terkunci (gated): setiap tahap hanya dapat dijalankan apabila
tahap sebelumnya telah selesai. Sistem mencakup manajemen data peserta, konfirmasi
kehadiran, pemeriksaan PLP, pemeriksaan dokter, dan rekomendasi kelulusan kesehatan.

---

## C. PENGGUNA SISTEM (USER ROLES)

Terdapat 5 role pengguna dalam sistem:

| No | Role | Kode | Fungsi Utama |
|---|---|---|---|
| 1 | Super Admin | superadmin | Kelola semua data, konfigurasi sistem, manajemen user |
| 2 | Admin | admin | Upload & input data peserta, kelola data master |
| 3 | Petugas Pendaftaran | pendaftaran | Konfirmasi kehadiran peserta di lokasi uji kesehatan |
| 4 | PLP | plp | Input pemeriksaan anamnesa & antropometri (item 1–2) |
| 5 | Dokter | dokter | Input pemeriksaan fisik (item 3–20) + kesimpulan akhir |

---

## D. DATA PENGGUNA (TABEL USERS)

Setiap akun pengguna menyimpan:

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT (PK) | Auto increment |
| nama | VARCHAR(100) | Nama petugas |
| role | ENUM | superadmin, admin, pendaftaran, plp, dokter |
| username | VARCHAR(50) | Unik |
| password | VARCHAR(255) | Terenkripsi (bcrypt) |
| no_telp | VARCHAR(20) | Opsional |

---

## E. DATA MAHASISWA (PESERTA)

Data yang diinput/diupload oleh admin sebelum hari pelaksanaan:

| No | Field | Tipe | Keterangan |
|---|---|---|---|
| 1 | Nama lengkap | VARCHAR(100) | Wajib |
| 2 | Jenis kelamin | ENUM (L/P) | Wajib |
| 3 | Prodi/Jurusan pilihan | VARCHAR(100) | Wajib, karena ada perbedaan indikator per prodi |
| 4 | Tempat lahir | VARCHAR(100) | Wajib |
| 5 | Tanggal lahir | DATE | Wajib |
| 6 | Alamat | TEXT | Wajib |
| 7 | Nomor pendaftaran Sipenmaru | VARCHAR(30) | Wajib, unik |
| 8 | Status kehadiran | ENUM | belum_hadir / hadir / tidak_hadir |
| 9 | Status pemeriksaan PLP | ENUM | belum / selesai |
| 10 | Status pemeriksaan Dokter | ENUM | belum / selesai |
| 11 | Kesimpulan akhir | ENUM | - / memenuhi_syarat / tidak_memenuhi_syarat |

---

## F. ALUR PROSES LENGKAP SISTEM

### ============================================
### TAHAP 1 — ADMIN: Upload & Input Data Peserta
### ============================================

**Actor:** Admin / Superadmin
**Kondisi awal:** Sistem kosong / data belum ada

**Proses:**
1. Admin login ke sistem.
2. Admin mengupload data peserta secara massal (import Excel/CSV) ATAU
   menginput satu per satu melalui form.
3. Data yang diinput mencakup seluruh field data mahasiswa (poin E).
4. Setelah tersimpan, status default setiap peserta:
   - status_kehadiran    = "belum_hadir"
   - status_plp          = "belum"
   - status_dokter       = "belum"
   - kesimpulan_akhir    = "-"

**Output:** Daftar peserta tersedia di sistem, siap untuk proses berikutnya.

---

### ============================================
### TAHAP 2 — PETUGAS PENDAFTARAN: Konfirmasi Kehadiran
### ============================================

**Actor:** Petugas Pendaftaran
**Kondisi yang diperlukan:** Data peserta sudah diinput admin (Tahap 1)

**Proses:**
1. Peserta datang ke lokasi uji kesehatan membawa bukti pendaftaran.
2. Petugas pendaftaran login ke modul "Konfirmasi Kehadiran".
3. Petugas mencari peserta berdasarkan:
   - Nomor pendaftaran Sipenmaru, ATAU
   - Nama peserta.
4. Sistem menampilkan data peserta yang cocok.
5. Petugas memverifikasi identitas peserta secara visual (cocokkan KTP/dokumen).
6. Petugas mengklik tombol "Konfirmasi Hadir".
7. Sistem mengubah status_kehadiran peserta menjadi "hadir".
8. Sistem otomatis memasukkan peserta ke dalam antrian ruang tunggu PLP.
9. Peserta diarahkan ke ruang tunggu oleh petugas.

**Aturan:**
- Peserta dengan status_kehadiran = "hadir" SAJA yang muncul
  di daftar antrian PLP.
- Peserta yang belum dikonfirmasi tidak akan muncul di modul PLP maupun Dokter.

**Output:** Peserta masuk antrian ruang tunggu, siap dipanggil PLP.

---

### ============================================
### TAHAP 3 — PLP: Pemeriksaan Anamnesa & Antropometri
### ============================================

**Actor:** PLP (Pranata Laboratorium Pendidikan)
**Kondisi yang diperlukan:** status_kehadiran peserta = "hadir"

**Daftar peserta yang muncul di modul PLP:**
  → Hanya peserta dengan status_kehadiran = "hadir"
     DAN status_plp = "belum"

**Proses:**
1. PLP login ke modul "Pemeriksaan PLP".
2. Sistem menampilkan daftar peserta yang sudah konfirmasi hadir
   dan belum diperiksa PLP.
3. PLP memanggil satu peserta dari antrian.
4. PLP mengisi form pemeriksaan:

#### Item 1: Anamnesa
| Sub-item | Jenis Input | Keterangan |
|---|---|---|
| Riwayat penyakit terdahulu | Text | Isian bebas |
| Suhu | Numerik | Nilai °C |
| Tensi | Text | Format: sistol/diastol mmHg |
| Riwayat kesehatan keluarga | Text | Isian bebas |
| Buta warna | Enum | Tidak buta warna / Buta warna parsial / Buta warna total |

#### Item 2: Antropometri
| Sub-item | Jenis Input | Keterangan |
|---|---|---|
| Tinggi badan | Numerik (cm) | Laki-laki >160 cm, Perempuan >150 cm |
| Berat badan | Numerik (kg) | Isian manual |
| BMI | Otomatis | Sistem hitung: BB / (TB_meter)² |

5. PLP menyimpan data dan menandai "Selesai Pemeriksaan PLP".
6. Sistem mengubah status_plp peserta menjadi "selesai".
7. Peserta diarahkan ke ruang tunggu pemeriksaan Dokter.

**Aturan:**
- PLP tidak dapat mengakses modul Dokter.
- Setelah status_plp = "selesai", nama peserta OTOMATIS muncul
  di antrian modul Dokter.

**Output:** Data anamnesa & antropometri tersimpan,
peserta masuk antrian Dokter.

---

### ============================================
### TAHAP 4 — DOKTER: Pemeriksaan Fisik Lengkap
### ============================================

**Actor:** Dokter
**Kondisi yang diperlukan:** status_plp peserta = "selesai"

**Daftar peserta yang muncul di modul Dokter:**
  → Hanya peserta dengan status_plp = "selesai"
     DAN status_dokter = "belum"

**Proses:**
1. Dokter login ke modul "Pemeriksaan Dokter".
2. Sistem menampilkan daftar peserta yang sudah selesai diperiksa PLP
   dan belum diperiksa Dokter.
3. Dokter memanggil satu peserta.
4. Dokter dapat melihat hasil pemeriksaan PLP (anamnesa & antropometri)
   sebagai referensi (read-only).
5. Dokter mengisi form pemeriksaan fisik item 3–19:

#### Item 3: Kulit
| Jenis Input | Pilihan |
|---|---|
| Pilihan | Putih / Kuning / Hitam / Sawo matang |

#### Item 4: Mata
| Sub-item | Jenis Input | Pilihan/Format |
|---|---|---|
| Kacamata | Pilihan | Berkacamata / Tidak berkacamata |
| Minus | Ya/Tidak + Nilai | Jika ya, isi nilai minus |
| Silindris | Ya/Tidak + Nilai | Jika ya, isi nilai silindris |
| Strabismus | Ya/Tidak + Nilai | Jika ya, isi nilai strabismus |

#### Item 5: Telinga
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Telinga kiri | Mendengar jelas / Tidak bisa mendengar | Ada kolom keterangan |
| Telinga kanan | Mendengar jelas / Tidak bisa mendengar | Ada kolom keterangan |

#### Item 6: Hidung (Pernafasan cuping hidung)
| Pilihan | Keterangan |
|---|---|
| Ya / Tidak | Ada kolom keterangan |

#### Item 7: Lidah
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Kebersihan | Bersih / Kurang bersih / Kotor | Ada kolom keterangan |
| Stomatitis | Ya / Tidak | Ada kolom keterangan |

#### Item 8: Pharing
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Nyeri tekan | Ya / Tidak | Ada kolom keterangan |

#### Item 9: Tonsil
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Kemerahan | Ya / Tidak | Ada kolom keterangan |
| Pembesaran | Ya / Tidak | Tidak ada kriteria kelulusan |

#### Item 10: Gigi
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Gigi lengkap | Ya / Tidak | Tidak ada kriteria kelulusan |

#### Item 11: Tiroid
| Jenis Input | Keterangan |
|---|---|
| Text | Tidak ada kriteria kelulusan |

#### Item 12: Jantung
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Mur-mur | Ya / Tidak | Ada kolom keterangan |

#### Item 13: Paru-paru
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Suara tambahan nafas | Ya / Tidak | Tidak ada kriteria |

#### Item 14: Palpasi Abdomen
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Hamil | Ya / Tidak | Default: Tidak hamil |

#### Item 15: Refleks Pupil
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Pupil | Isokor / Anisokor | - |

#### Item 16: Thorax Photo
| Jenis Input | Keterangan |
|---|---|
| Upload file + Text | Hasil rontgen maks. 1 bulan terakhir; ada kolom keterangan |

#### Item 17: Gangguan Tulang Belakang
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Skoliosis | Ya / Tidak | Ada kolom keterangan |
| Lordosis | Ya / Tidak | Ada kolom keterangan |
| Kifosis | Ya / Tidak | Ada kolom keterangan |
| Lainnya | Ya / Tidak | Ada kolom keterangan |

#### Item 18: Kemampuan Bicara
| Sub-item | Pilihan | Keterangan |
|---|---|---|
| Artikulasi | Artikulasi jelas / Tidak jelas | Ada kolom keterangan |

#### Item 19: Cacat Tubuh yang Dapat Mengganggu Tugas
| Jenis Input | Keterangan |
|---|---|
| Text | Isian bebas |

6. Setelah semua item terisi, Dokter mengisi Kesimpulan Akhir (Item 20):

#### Item 20: Kesimpulan (Rekomendasi Dokter)
| Pilihan | Aturan |
|---|---|
| Memenuhi Syarat | Keterangan opsional |
| Tidak Memenuhi Syarat | Keterangan WAJIB diisi |

7. Dokter menyimpan dan mengunci data.
8. Sistem mengubah status_dokter = "selesai" dan mengisi kesimpulan_akhir.

**Aturan:**
- Dokter tidak dapat mengubah data PLP.
- Setelah disimpan, data terkunci; hanya superadmin yang dapat membuka kunci.
- Keterangan wajib diisi jika kesimpulan = "Tidak Memenuhi Syarat".

**Output:** Pemeriksaan selesai, kesimpulan tersimpan.

---

### ============================================
### TAHAP 5 — ADMIN/SUPERADMIN: Rekap & Laporan
### ============================================

**Actor:** Admin / Superadmin
**Kondisi yang diperlukan:** status_dokter = "selesai"

**Fitur:**
1. Melihat rekap seluruh peserta beserta status tiap tahap.
2. Filter berdasarkan: prodi, status kehadiran, status pemeriksaan, kesimpulan.
3. Ekspor data ke Excel/PDF:
   - Rekap seluruh peserta + kesimpulan.
   - Daftar peserta Memenuhi Syarat.
   - Daftar peserta Tidak Memenuhi Syarat beserta keterangannya.

---

## G. DIAGRAM STATUS PESERTA

Status peserta bergerak secara linear:

  [Data Diinput Admin]
         ↓
  status_kehadiran = "belum_hadir"
         ↓ (Petugas Pendaftaran konfirmasi)
  status_kehadiran = "hadir"  ← Muncul di antrian PLP
         ↓ (PLP selesai periksa)
  status_plp = "selesai"      ← Muncul di antrian Dokter
         ↓ (Dokter selesai periksa)
  status_dokter = "selesai"
  kesimpulan = "Memenuhi Syarat" / "Tidak Memenuhi Syarat"

---

## H. KEBUTUHAN DATABASE (RINGKAS)

### 1. tabel_users
Menyimpan: id, nama, role, username, password, no_telp

### 2. tabel_mahasiswa
Menyimpan: id, no_pendaftaran, nama, jenis_kelamin, prodi_pilihan,
           tempat_lahir, tanggal_lahir, alamat,
           status_kehadiran, status_plp, status_dokter,
           kesimpulan_akhir, keterangan_kesimpulan,
           created_at, updated_at

### 3. tabel_pemeriksaan_plp
Menyimpan: id, mahasiswa_id, plp_id, tgl_periksa,
           riwayat_penyakit, suhu, tensi, riwayat_keluarga, buta_warna,
           tinggi_badan, berat_badan, bmi

### 4. tabel_pemeriksaan_dokter
Menyimpan: id, mahasiswa_id, dokter_id, tgl_periksa,
           [seluruh item 3–19 sesuai detail di atas],
           kesimpulan, keterangan_kesimpulan, is_locked

### 5. tabel_log_aktivitas
Menyimpan: id, user_id, aksi, target_id, target_tabel, waktu
(Untuk keperluan audit trail setiap perubahan data)

---

## I. ATURAN BISNIS (BUSINESS RULES)

| No | Aturan |
|---|---|
| 1 | Peserta HANYA muncul di antrian PLP jika status_kehadiran = "hadir" |
| 2 | Peserta HANYA muncul di antrian Dokter jika status_plp = "selesai" |
| 3 | PLP tidak dapat mengakses form pemeriksaan Dokter |
| 4 | Dokter tidak dapat mengakses form pemeriksaan PLP |
| 5 | Keterangan kesimpulan WAJIB diisi jika = "Tidak Memenuhi Syarat" |
| 6 | BMI dihitung otomatis oleh sistem |
| 7 | Setelah Dokter simpan kesimpulan, data terkunci (is_locked = true) |
| 8 | Hanya superadmin yang dapat membuka kunci data yang sudah final |
| 9 | Semua aksi penting tersimpan di tabel log_aktivitas |

---

## J. KEBUTUHAN NON-FUNGSIONAL

| No | Aspek | Kebutuhan |
|---|---|---|
| 1 | Keamanan | Password bcrypt, role-based access control |
| 2 | Ketersediaan | Dapat diakses via browser, responsif di mobile |
| 3 | Performa | Respon form < 3 detik |
| 4 | Kemudahan | UI sederhana, antrian tampil real-time |
| 5 | Backup | Data dapat diekspor Excel/PDF |
| 6 | Audit | Setiap aksi tercatat (siapa, kapan, apa) |

---

## K. REKOMENDASI TEKNOLOGI

| Komponen | Rekomendasi |
|---|---|
| Backend | Laravel (PHP) |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL / PostgreSQL |
| Hosting | DigitalOcean / Supabase / Azure |
| Auth & Role | Spatie Laravel Permission |
| Export | Laravel Excel + DomPDF |
| Real-time antrian | Laravel Broadcasting / Polling |

---

*Dokumen ini disusun berdasarkan data kebutuhan pemeriksaan fisik Sipenmaru Poltekkes Kemenkes.*
*Versi 2.0 – Revisi alur: penambahan role Petugas Pendaftaran dan sistem antrian gated.*
