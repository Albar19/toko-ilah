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
| File ini berisi definisi semua route/URL yang dapat diakses via web browser
| Route dikelompokkan berdasarkan middleware untuk keamanan
*/

// Mengarahkan halaman utama ke halaman login
Route::get('/', function () {
    return redirect()->route('login');  // Redirect root URL ke halaman login
});

// Dashboard - halaman utama setelah login
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])    // Middleware: harus login dan email terverifikasi
    ->name('dashboard');                  // Named route untuk referensi

// Profile Management (hanya untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');       // Form edit profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Update profil
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Hapus akun
});

// Produk Management (CRUD dengan Resource Controller)
Route::middleware(['auth'])->group(function () {
    Route::resource('produk', ProductController::class);  // Generate 7 route CRUD otomatis:
    // GET /produk (index), GET /produk/create (create), POST /produk (store),
    // GET /produk/{id} (show), GET /produk/{id}/edit (edit), 
    // PUT/PATCH /produk/{id} (update), DELETE /produk/{id} (destroy)
});

// Penjualan Management (CRUD manual untuk kontrol lebih detail)
Route::middleware(['auth'])->group(function () {
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');           // Daftar penjualan
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');  // Form tambah penjualan
    Route::post('/penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');    // Simpan penjualan baru
    Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show'); // Detail penjualan
    Route::get('/penjualan/{penjualan}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit'); // Form edit penjualan
    Route::put('/penjualan/{penjualan}', [PenjualanController::class, 'update'])->name('penjualan.update');  // Update penjualan
    Route::delete('/penjualan/{penjualan}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy'); // Hapus penjualan
});

// Laporan & Analisis Bisnis (hanya untuk user login)
Route::middleware(['auth'])->group(function () {
    Route::get('/rekap', [PenjualanController::class, 'rekap'])->name('penjualan.rekap');                    // Rekap bulanan
    Route::get('/rekap/{bulan}', [PenjualanController::class, 'rekapDetail'])->name('penjualan.rekap.detail'); // Detail rekap per bulan
    Route::get('/rekap/{bulan}/pdf', [PenjualanController::class, 'rekapPdf'])->name('penjualan.rekap.pdf');   // Export rekap ke PDF
    Route::get('/analisis', [PenjualanController::class, 'analisis'])->name('penjualan.analisis');             // Analisis produk terlaris
    Route::get('/analisis/pdf', [PenjualanController::class, 'analisisPdf'])->name('penjualan.analisis.pdf');  // Export analisis ke PDF
});

// Keamanan: Disable registrasi user baru
Route::get('/register', function () {
    abort(403);  // Return HTTP 403 Forbidden jika ada yang coba akses /register
});

/* Route khusus registrasi di-disable hapus ini agar aktif kembali
// Komen ini menunjukkan cara mengaktifkan kembali registrasi jika diperlukan
Route::get('/register', function () {
    abort(403);
});
*/

// Include route otentikasi bawaan Laravel (login, logout, forgot password, dll)
require __DIR__.'/auth.php';
