<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();
        $bulanIni = Carbon::now()->format('Y-m');

        $jumlahProduk = Product::count();

        $transaksiHariIni = Penjualan::whereDate('tanggal', $hariIni)->count();
        $omsetHariIni = Penjualan::whereDate('tanggal', $hariIni)->sum('total_harga');
        $omsetBulanIni = Penjualan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulanIni])->sum('total_harga');

        $produkTerlaris = DB::table('penjualan_details')
            ->join('penjualans', 'penjualans.id', '=', 'penjualan_details.penjualan_id')
            ->whereRaw("DATE_FORMAT(penjualans.tanggal, '%Y-%m') = ?", [$bulanIni])
            ->select('product_id', DB::raw('SUM(qty) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->first();

        $produkTerlarisNama = $produkTerlaris ? Product::find($produkTerlaris->product_id)?->nama_produk : '-';
        $produkTerlarisQty = $produkTerlaris->total ?? 0;

        return view('dashboard.index', compact(
            'jumlahProduk',
            'transaksiHariIni',
            'omsetHariIni',
            'omsetBulanIni',
            'produkTerlarisNama',
            'produkTerlarisQty'
        ));
    }
}
