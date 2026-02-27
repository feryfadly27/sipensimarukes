<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PlpController;
use App\Http\Controllers\DokterController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes with cache prevention
Route::middleware(['auth', 'prevent_cache'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Data Peserta Management (Admin & Superadmin)
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::post('mahasiswa/import', [MahasiswaController::class, 'importExcel'])->name('mahasiswa.importExcel');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });
    
    // Pendaftaran validasi (now part of dashboard but keeping route for modal submission)
    Route::post('/pendaftaran/{mahasiswa}/validasi', [PendaftaranController::class, 'validasi'])->name('pendaftaran.validasi');
    
    // PLP examination (new route for PLP form submission)
    Route::post('/plp/{mahasiswa}', [PlpController::class, 'store'])->name('plp.store');
    Route::get('/plp/check-ongoing', [PlpController::class, 'checkOngoing'])->name('plp.checkOngoing');
    Route::post('/plp/{mahasiswa}/start', [PlpController::class, 'startExamination'])->name('plp.start');
    Route::get('/plp/{mahasiswa}/verify', [PlpController::class, 'verifyStudent'])->name('plp.verify');

    // Dokter examination
    Route::get('/dokter/selesai', [DokterController::class, 'completed'])->name('dokter.completed')->middleware('role:dokter');
    Route::get('/dokter/selesai/{mahasiswa}', [DokterController::class, 'show'])->name('dokter.show')->middleware('role:dokter');
    Route::get('/dokter/selesai/{mahasiswa}/cetak', [DokterController::class, 'print'])->name('dokter.print')->middleware('role:dokter');
    Route::get('/dokter/{mahasiswa}/periksa', [DokterController::class, 'form'])->name('dokter.form')->middleware('role:dokter');
    Route::post('/dokter/{mahasiswa}', [DokterController::class, 'store'])->name('dokter.store')->middleware('role:dokter');
    
    // Placeholder routes
    Route::get('/plp', [DashboardController::class, 'index'])->name('plp.index');
    Route::get('/dokter', [DashboardController::class, 'index'])->name('dokter.index');
    Route::get('/log', [LogAktivitasController::class, 'index'])->name('logs.index');
    Route::get('/users', [DashboardController::class, 'index'])->name('users.index');
});

// File download routes (separate from prevent_cache to avoid header conflicts)
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('mahasiswa/template/excel', [MahasiswaController::class, 'templateExcel'])->name('mahasiswa.templateExcel');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
});

