<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function create()
    {
        $products = Product::all();
        return view('penjualan.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'produk_id.*' => 'required|exists:products,id',
            'qty.*' => 'required|integer|min:1',
            'harga.*' => 'required|integer|min:0',
            'subtotal.*' => 'required|integer|min:0',
        ]);

        $penjualan = Penjualan::create([
            'tanggal' => $request->tanggal,
            'total_harga' => array_sum($request->subtotal),
        ]);

        foreach ($request->produk_id as $i => $id_produk) {
            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'product_id' => $id_produk,
                'qty' => $request->qty[$i],
                'harga_satuan' => $request->harga[$i],
                'subtotal' => $request->subtotal[$i],
            ]);
        }

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');
    }

    public function index()
    {
        $penjualans = Penjualan::orderBy('tanggal', 'desc')->get();
        return view('penjualan.index', compact('penjualans'));
    }

    public function show($id)
    {
        $penjualan = Penjualan::with('details.product')->findOrFail($id);
        return view('penjualan.show', compact('penjualan'));
    }

    public function rekap()
    {
        $rekap = Penjualan::select(
            DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),
            DB::raw("COUNT(*) as jumlah_transaksi"),
            DB::raw("SUM(total_harga) as total_penjualan")
        )
        ->groupBy('bulan')
        ->orderBy('bulan', 'desc')
        ->get();

        return view('penjualan.rekap', compact('rekap'));
    }

    public function rekapDetail($bulan)
    {
        $penjualans = Penjualan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])->get();

        return view('penjualan.rekap_detail', [
            'penjualans' => $penjualans,
            'bulan' => $bulan
        ]);
    }

    public function analisis(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));

        // Produk terjual di bulan tertentu
        $produkTerlaris = DB::table('penjualan_details')
            ->join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
            ->whereRaw("DATE_FORMAT(penjualans.tanggal, '%Y-%m') = ?", [$bulan])
            ->select('penjualan_details.product_id', DB::raw('SUM(qty) as total_terjual'))
            ->groupBy('penjualan_details.product_id')
            ->orderByDesc('total_terjual')
            ->get()
            ->map(function ($item) {
                $item->nama_produk = \App\Models\Product::find($item->product_id)?->nama_produk ?? 'Tidak Diketahui';
                return $item;
            });

        // Produk tidak pernah dibeli pada bulan itu
        $produkTerjualIds = $produkTerlaris->pluck('product_id')->toArray();
        $produkTidakLaku = \App\Models\Product::whereNotIn('id', $produkTerjualIds)->get();

        // Untuk chart
        $labels = $produkTerlaris->pluck('nama_produk');
        $data = $produkTerlaris->pluck('total_terjual');

        return view('penjualan.analisis', compact('produkTerlaris', 'produkTidakLaku', 'labels', 'data', 'bulan'));
    }

    public function analisisPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        $produkTerlaris = DB::table('penjualan_details')
            ->join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
            ->whereRaw("DATE_FORMAT(penjualans.tanggal, '%Y-%m') = ?", [$bulan])
            ->select('penjualan_details.product_id', DB::raw('SUM(qty) as total_terjual'))
            ->groupBy('penjualan_details.product_id')
            ->orderByDesc('total_terjual')
            ->get()
            ->map(function ($item) {
                $item->nama_produk = \App\Models\Product::find($item->product_id)?->nama_produk ?? 'Tidak Diketahui';
                return $item;
            });

        $produkTerjualIds = $produkTerlaris->pluck('product_id')->toArray();
        $produkTidakLaku = \App\Models\Product::whereNotIn('id', $produkTerjualIds)->get();

        $pdf = Pdf::loadView('penjualan.analisis_pdf', compact('produkTerlaris', 'produkTidakLaku', 'bulan'));

        return $pdf->download('analisis-produk-'.$bulan.'.pdf');
    }

    public function rekapPdf($bulan)
    {
        $penjualans = Penjualan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->orderBy('tanggal')
            ->get();

        $total = $penjualans->sum('total_harga');

        $pdf = Pdf::loadView('penjualan.rekap_pdf', [
            'penjualans' => $penjualans,
            'bulan' => $bulan,
            'total' => $total,
        ]);

        return $pdf->download('rekap-bulanan-'.$bulan.'.pdf');
    }
}
