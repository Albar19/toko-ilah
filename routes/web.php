<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Mengarahkan halaman utama ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile (hanya untuk user login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Produk (resource, hanya untuk user login)
Route::middleware(['auth'])->group(function () {
    Route::resource('produk', ProductController::class);
});

// Penjualan (CRUD, hanya untuk user login)
Route::middleware(['auth'])->group(function () {
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::get('/penjualan/{penjualan}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
    Route::put('/penjualan/{penjualan}', [PenjualanController::class, 'update'])->name('penjualan.update');
    Route::delete('/penjualan/{penjualan}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
});

// Laporan & Analisis (hanya untuk user login)
Route::middleware(['auth'])->group(function () {
    Route::get('/rekap', [PenjualanController::class, 'rekap'])->name('penjualan.rekap');
    Route::get('/rekap/{bulan}', [PenjualanController::class, 'rekapDetail'])->name('penjualan.rekap.detail');
    Route::get('/rekap/{bulan}/pdf', [PenjualanController::class, 'rekapPdf'])->name('penjualan.rekap.pdf');
    Route::get('/analisis', [PenjualanController::class, 'analisis'])->name('penjualan.analisis');
    Route::get('/analisis/pdf', [PenjualanController::class, 'analisisPdf'])->name('penjualan.analisis.pdf');
});

// Route khusus registrasi di-disable
Route::get('/register', function () {
    abort(403);
});

// Include route otentikasi (login, logout, dll)
require __DIR__.'/auth.php';
