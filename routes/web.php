<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;

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
        Route::get('mahasiswa/template/excel', [MahasiswaController::class, 'templateExcel'])->name('mahasiswa.templateExcel');
    });
    
    // Placeholder routes untuk menu (akan dibuat nanti)
    Route::get('/pendaftaran', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('pendaftaran.index');
    
    Route::get('/plp', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('plp.index');
    
    Route::get('/dokter', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('dokter.index');
    
    Route::get('/laporan', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('laporan.index');
    
    Route::get('/log', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('log.index');
    
    Route::get('/users', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('users.index');
});

