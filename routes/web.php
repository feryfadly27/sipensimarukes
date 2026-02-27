<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

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
    
    // Placeholder routes untuk menu (akan dibuat nanti)
    Route::get('/mahasiswa', function() {
        return view('dashboard.index')->with('stats', [])->with('recent_activities', []);
    })->name('mahasiswa.index');
    
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

