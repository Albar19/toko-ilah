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
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/register', function () {
    abort(403);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('produk', ProductController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');
});

Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');

Route::get('/rekap', [PenjualanController::class, 'rekap'])->name('penjualan.rekap');
Route::get('/rekap/{bulan}', [PenjualanController::class, 'rekapDetail'])->name('penjualan.rekap.detail');

Route::get('/analisis', [PenjualanController::class, 'analisis'])->name('penjualan.analisis');

Route::get('/analisis/pdf', [PenjualanController::class, 'analisisPdf'])->name('penjualan.analisis.pdf');

Route::get('/rekap/{bulan}/pdf', [PenjualanController::class, 'rekapPdf'])->name('penjualan.rekap.pdf');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

