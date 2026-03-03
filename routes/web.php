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
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdiController;

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
    Route::get('/login-success', [LoginController::class, 'showLoginSuccess'])->name('login.success');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Data Peserta Management (Admin & Superadmin)
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::post('mahasiswa/import', [MahasiswaController::class, 'importExcel'])->name('mahasiswa.importExcel');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/ringkas', [LaporanController::class, 'ringkas'])->name('laporan.ringkas');
        Route::get('/laporan/ringkas/export', [LaporanController::class, 'exportRingkas'])->name('laporan.ringkas.export');
    });
    
    // Pendaftaran validasi (now part of dashboard but keeping route for modal submission)
    Route::post('/pendaftaran/{mahasiswa}/validasi', [PendaftaranController::class, 'validasi'])
        ->name('pendaftaran.validasi')
        ->middleware('role:pendaftaran,superadmin');
    
    // PLP examination (new route for PLP form submission)
    Route::post('/plp/{mahasiswa}', [PlpController::class, 'store'])->name('plp.store')->middleware('role:plp,superadmin');
    Route::get('/plp/check-ongoing', [PlpController::class, 'checkOngoing'])->name('plp.checkOngoing')->middleware('role:plp,superadmin');
    Route::post('/plp/{mahasiswa}/start', [PlpController::class, 'startExamination'])->name('plp.start')->middleware('role:plp,superadmin');
    Route::get('/plp/{mahasiswa}/verify', [PlpController::class, 'verifyStudent'])->name('plp.verify')->middleware('role:plp,superadmin');

    // Dokter examination
    Route::get('/dokter/selesai', [DokterController::class, 'completed'])->name('dokter.completed')->middleware('role:dokter,superadmin');
    Route::get('/dokter/selesai/{mahasiswa}', [DokterController::class, 'show'])->name('dokter.show')->middleware('role:dokter,superadmin');
    Route::get('/dokter/selesai/{mahasiswa}/cetak', [DokterController::class, 'print'])->name('dokter.print')->middleware('role:dokter,superadmin');
    Route::get('/dokter/{mahasiswa}/periksa', [DokterController::class, 'form'])->name('dokter.form')->middleware('role:dokter,superadmin');
    Route::post('/dokter/{mahasiswa}', [DokterController::class, 'store'])->name('dokter.store')->middleware('role:dokter,superadmin');
    
    // Placeholder routes
    Route::get('/pendaftaran', [DashboardController::class, 'index'])->name('pendaftaran.index')->middleware('role:pendaftaran,superadmin');
    Route::get('/plp', [DashboardController::class, 'index'])->name('plp.index')->middleware('role:plp,superadmin');
    Route::get('/dokter', [DashboardController::class, 'index'])->name('dokter.index')->middleware('role:dokter,superadmin');
    Route::get('/log', [LogAktivitasController::class, 'index'])->name('logs.index');
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/prodis', [ProdiController::class, 'index'])->name('prodis.index');
        Route::post('/prodis', [ProdiController::class, 'store'])->name('prodis.store');
        Route::put('/prodis/{prodi}', [ProdiController::class, 'update'])->name('prodis.update');
        Route::patch('/prodis/{prodi}/toggle', [ProdiController::class, 'toggle'])->name('prodis.toggle');
        Route::delete('/prodis/{prodi}', [ProdiController::class, 'destroy'])->name('prodis.destroy');
    });
});

// File download routes (separate from prevent_cache to avoid header conflicts)
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('mahasiswa/template/excel', [MahasiswaController::class, 'templateExcel'])->name('mahasiswa.templateExcel');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
});

