# SIPENSIMARUKES

Sistem Informasi Uji Kesehatan untuk pengelolaan peserta, pemeriksaan PLP, pemeriksaan dokter, dan pelaporan hasil.

## Ringkasan Fitur

- Login berbasis role.
- Dashboard operasional sesuai role user.
- Manajemen data peserta (CRUD + import Excel).
- Alur validasi pendaftaran.
- Alur pemeriksaan PLP.
- Alur pemeriksaan dokter + riwayat peserta sudah diperiksa.
- Laporan detail dan laporan ringkas.
- Export laporan ke Excel (detail dan ringkas).


## Role dan Akses


- `admin`: data peserta, laporan, export.
- `pendaftaran`: validasi pendaftaran peserta.
- `nakes`: pemeriksaan PLP.
- `dokter`: pemeriksaan dokter dan daftar selesai.

## Teknologi

- PHP `^8.2`
- Laravel `^12`
- MySQL
- Blade + Tailwind CSS (CDN)
- PhpSpreadsheet untuk export Excel

## Struktur Modul Utama

- `app/Http/Controllers/`
	- `DashboardController.php`
	- `MahasiswaController.php`
	- `PendaftaranController.php`
	- `PlpController.php`
	- `DokterController.php`
	- `LaporanController.php`
	- `UserController.php`
- `resources/views/`
	- `dashboard/`
	- `mahasiswa/`
	- `dokter/`
	- `laporan/`
	- `users/`
	- `auth/`

## Cara Menjalankan (Quick Start)

1. Install dependency backend dan frontend:

```bash
composer install
npm install
```

2. Siapkan file environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Atur koneksi database pada `.env` (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4. Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

5. Jalankan aplikasi:

```bash
./start.sh
```

Atau manual:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Akun Default Seeder

Semua password default: `password`

- `admin`
- `pendaftaran`
- `nakes`
- `dokter`

## Perintah Penting

- Clear cache dan reload migrasi terbaru:

```bash
./reload.sh
```

- Menjalankan test:

```bash
php artisan test
```

- Build asset produksi:

```bash
npm run build
```

## Catatan Konfigurasi

- Untuk produksi, set:
	- `APP_ENV=production`
	- `APP_DEBUG=false`
	- kredensial database yang aman
- Jangan commit file `.env` ke repository.
- `APP_KEY` bersifat rahasia dan tidak boleh dibagikan.

## Alur Singkat Operasional

1. Peserta masuk/diinput ke data peserta.
2. Pendaftaran melakukan validasi awal.
3. PLP melakukan pemeriksaan laboratorium.
4. Dokter melakukan pemeriksaan lanjutan dan menetapkan kesimpulan.
5. Admin memantau laporan detail dan ringkas, lalu export bila diperlukan.

## Changelog

Riwayat perubahan ringkas tersedia di `CHANGELOG.md`.
