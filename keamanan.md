# Catatan Keamanan Sistem SIPENSIMARUKES

Tanggal review: 27 Februari 2026

## Ringkasan
Dokumen ini berisi temuan keamanan dari konfigurasi dan kode saat ini, dikelompokkan berdasarkan tingkat risiko: **Kritis**, **Tinggi**, **Sedang**, dan **Rendah**.

---

## 1) Kritis

### 1.1 Konfigurasi produksi belum hardening
- Kondisi saat ini (runtime):
  - `APP_DEBUG=true`
  - `SESSION_SECURE_COOKIE=false`
  - kredensial sensitif aktif di environment (`APP_KEY`, `DB_PASSWORD`).
- Dampak:
  - Potensi kebocoran detail error sensitif.
  - Cookie sesi dapat terkirim tanpa proteksi HTTPS pada skenario tertentu.
  - Risiko pengambilalihan sesi dan ekspos data.
- Rekomendasi:
  - Gunakan `APP_ENV=production`.
  - Set `APP_DEBUG=false`.
  - Set `SESSION_SECURE_COOKIE=true`.
  - Rotasi `APP_KEY` dan kredensial database saat deployment.

### 1.2 Password default statis untuk akun seed
- Kondisi saat ini:
  - Seeder user masih menggunakan password default mudah ditebak untuk beberapa role.
- Dampak:
  - Risiko akses tidak sah jika data seed dijalankan pada environment non-dev.
- Rekomendasi:
  - Hindari password statis di seeder.
  - Gunakan password acak + mekanisme reset password awal.
  - Batasi penggunaan seeder hanya untuk local/dev.

---

## 2) Tinggi

### 2.1 Belum ada rate limit login
- Kondisi saat ini:
  - Endpoint login belum menggunakan throttle/rate limiter.
- Dampak:
  - Rentan brute-force credential stuffing.
- Rekomendasi:
  - Tambahkan middleware `throttle` pada route login.
  - Atau implementasikan `RateLimiter` berbasis username + IP.

### 2.2 Script commit otomatis berisiko mendorong file sensitif
- Kondisi saat ini:
  - Script `push_commit.sh` menggunakan `git add -A`.
- Dampak:
  - Potensi file sensitif ikut ter-commit jika guard `.gitignore` berubah.
- Rekomendasi:
  - Tambahkan deny-list file sensitif (`.env`, key, dump DB, credential files).
  - Tampilkan konfirmasi final sebelum commit/push.

---

## 3) Sedang

### 3.1 Route logout belum dibatasi middleware auth
- Kondisi saat ini:
  - Route logout didefinisikan di luar grup `auth`.
- Dampak:
  - Tidak langsung kritis, namun lebih aman bila hanya dapat dipanggil user terautentikasi.
- Rekomendasi:
  - Pindahkan route logout ke grup middleware `auth`.

### 3.2 Script startup membaca kredensial dari `.env`
- Kondisi saat ini:
  - `start.sh` dan `startpublic.sh` membaca `DB_USERNAME`/`DB_PASSWORD` langsung dari `.env`.
- Dampak:
  - Aman untuk lokal, tetapi perlu kontrol permission file dan host yang ketat.
- Rekomendasi:
  - Gunakan environment runtime yang terisolasi.
  - Batasi akses file `.env` (permission minimal).

---

## 4) Rendah

### 4.1 Informasi akun default ditampilkan di output seeder
- Dampak:
  - Menambah paparan informasi operasional.
- Rekomendasi:
  - Hapus detail kredensial dari output command seeder.

### 4.2 Locale default masih `en`
- Dampak:
  - Bukan celah keamanan langsung, tetapi mempengaruhi konsistensi log/UX.
- Rekomendasi:
  - Sesuaikan locale ke kebutuhan operasional (mis. `id`).

---

## Prioritas Tindak Lanjut (Direkomendasikan)
1. Hardening environment produksi (`APP_DEBUG`, cookie secure, rotasi secret).
2. Hilangkan password default statis di seeder.
3. Implementasi rate limiter login.
4. Perketat route logout dengan middleware `auth`.
5. Tambahkan guard file sensitif pada script commit/push.

---

## Catatan
- File `.env` saat ini tidak ter-track git (yang ter-track adalah `.env.example`).
- Ini menurunkan risiko kebocoran lewat repository, namun tidak menghilangkan risiko di runtime/deployment.
