<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DashboardController
 * Controller untuk mengelola halaman dashboard/beranda aplikasi
 * Menampilkan ringkasan data dan statistik penjualan
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data statistik
     */
    public function index()
    {
        // Mendapatkan tanggal hari ini dan format bulan saat ini
        $hariIni = Carbon::today();                    // Tanggal hari ini (2025-06-30)
        $bulanIni = Carbon::now()->format('Y-m');      // Format bulan ini (2025-06)

        // Menghitung total jumlah produk di database
        $jumlahProduk = Product::count();

        // Menghitung statistik penjualan hari ini
        $transaksiHariIni = Penjualan::whereDate('tanggal', $hariIni)->count();        // Jumlah transaksi hari ini
        $omsetHariIni = Penjualan::whereDate('tanggal', $hariIni)->sum('total_harga'); // Total omset hari ini

        // Menghitung omset bulan ini menggunakan raw SQL untuk format tanggal
        $omsetBulanIni = Penjualan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulanIni])->sum('total_harga');

        // Query untuk mencari produk terlaris bulan ini
        $produkTerlaris = DB::table('penjualan_details')
            ->join('penjualans', 'penjualans.id', '=', 'penjualan_details.penjualan_id')  // Join dengan tabel penjualans
            ->whereRaw("DATE_FORMAT(penjualans.tanggal, '%Y-%m') = ?", [$bulanIni])       // Filter bulan ini
            ->select('product_id', DB::raw('SUM(qty) as total'))                          // Pilih product_id dan total qty
            ->groupBy('product_id')                                                       // Group berdasarkan product_id
            ->orderByDesc('total')                                                        // Urutkan dari qty terbanyak
            ->first();                                                                    // Ambil data pertama (terlaris)

        // Mendapatkan nama produk terlaris dan qty-nya
        $produkTerlarisNama = $produkTerlaris ? Product::find($produkTerlaris->product_id)?->nama_produk : '-';
        $produkTerlarisQty = $produkTerlaris->total ?? 0;  // Jika tidak ada data, default 0

        // Mengirim semua data ke view dashboard
        return view('dashboard.index', compact(
            'jumlahProduk',        // Total produk
            'transaksiHariIni',    // Jumlah transaksi hari ini
            'omsetHariIni',        // Omset hari ini
            'omsetBulanIni',       // Omset bulan ini
            'produkTerlarisNama',  // Nama produk terlaris
            'produkTerlarisQty'    // Qty produk terlaris
        ));
    }
}
