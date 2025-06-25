<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;

class PenjualanController extends Controller
{
    public function create()
    {
        $products = Product::all();
        return view('penjualan.create', compact('products'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input dasar
        $request->validate([
            'tanggal' => 'required|date',
            'produk_id.*' => 'required|exists:products,id',
            'qty.*' => 'required|integer|min:1',
        ]);

        // 2. Agregasi produk untuk menggabungkan item yang sama
        $produkData = [];
        $totalHarga = 0;

        foreach ($request->produk_id as $i => $id_produk) {
            $qty = (int)$request->qty[$i];
            
            if (isset($produkData[$id_produk])) {
                $produkData[$id_produk]['qty'] += $qty;
            } else {
                $product = Product::find($id_produk);
                $produkData[$id_produk] = [
                    'qty' => $qty,
                    'harga' => $product->harga,
                    'nama' => $product->nama_produk,
                    'stok_saat_ini' => $product->stok,
                ];
            }
        }
        
        // 3. Validasi stok sebelum memulai transaksi
        foreach ($produkData as $id => $data) {
             if ($data['qty'] > $data['stok_saat_ini']) {
                throw ValidationException::withMessages([
                    'produk_id' => 'Stok untuk produk "' . $data['nama'] . '" tidak mencukupi. Sisa stok: ' . $data['stok_saat_ini']
                ]);
            }
            // Hitung subtotal dan total harga
            $subtotal = $data['qty'] * $data['harga'];
            $produkData[$id]['subtotal'] = $subtotal;
            $totalHarga += $subtotal;
        }

        // 4. Gunakan DB Transaction untuk memastikan integritas data
        try {
            DB::beginTransaction();

            // 5. Buat entri penjualan utama
            $penjualan = Penjualan::create([
                'tanggal' => $request->tanggal,
                'total_harga' => $totalHarga,
            ]);

            // 6. Simpan detail penjualan dan kurangi stok
            foreach ($produkData as $id_produk => $data) {
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'product_id' => $id_produk,
                    'qty' => $data['qty'],
                    'harga_satuan' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);

                // 7. Kurangi stok produk
                $product = Product::find($id_produk);
                $product->decrement('stok', $data['qty']);
            }

            DB::commit(); // Jika semua berhasil, simpan perubahan

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, batalkan semua perubahan
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penjualan: ' . $e->getMessage())->withInput();
        }
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

    /**
     * Menampilkan form untuk mengedit penjualan.
     */
    public function edit(Penjualan $penjualan)
    {
        // Load relasi details beserta product, dan ambil semua produk untuk dropdown
        $penjualan->load('details.product');
        $products = Product::all();

        return view('penjualan.edit', compact('penjualan', 'products'));
    }

    /**
     * Memperbarui data penjualan di database.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'produk_id.*' => 'required|exists:products,id',
            'qty.*' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Kembalikan stok dari penjualan lama
            foreach ($penjualan->details as $detail) {
                Product::find($detail->product_id)->increment('stok', $detail->qty);
            }
            
            // 2. Hapus detail penjualan yang lama
            $penjualan->details()->delete();

            // 3. Proses data baru (mirip seperti method store)
            $produkData = [];
            $totalHarga = 0;
            foreach ($request->produk_id as $i => $id_produk) {
                 $product = Product::find($id_produk);
                 $qty = (int)$request->qty[$i];

                 // Cek stok
                 if ($qty > $product->stok) {
                    throw new \Exception('Stok untuk produk "'.$product->nama_produk.'" tidak mencukupi.');
                 }
                 
                 $subtotal = $qty * $product->harga;
                 $totalHarga += $subtotal;

                 // Gabungkan produk yang sama
                 if (isset($produkData[$id_produk])) {
                     $produkData[$id_produk]['qty'] += $qty;
                     $produkData[$id_produk]['subtotal'] += $subtotal;
                 } else {
                     $produkData[$id_produk] = [
                        'qty' => $qty,
                        'harga_satuan' => $product->harga,
                        'subtotal' => $subtotal,
                     ];
                 }
            }

            // 4. Update data penjualan utama
            $penjualan->update([
                'tanggal' => $request->tanggal,
                'total_harga' => $totalHarga,
            ]);

            // 5. Buat detail penjualan baru dan kurangi stok
            foreach($produkData as $id_produk => $data) {
                $penjualan->details()->create([
                    'product_id' => $id_produk,
                    'qty' => $data['qty'],
                    'harga_satuan' => $data['harga_satuan'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Kurangi stok produk
                Product::find($id_produk)->decrement('stok', $data['qty']);
            }

            DB::commit();

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate penjualan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus data penjualan.
     */
    public function destroy(Penjualan $penjualan)
    {
        try {
            DB::beginTransaction();

            // Kembalikan stok produk dari detail penjualan yang akan dihapus
            foreach ($penjualan->details as $detail) {
                Product::find($detail->product_id)->increment('stok', $detail->qty);
            }

            // Hapus data penjualan (detail akan terhapus otomatis karena cascade)
            $penjualan->delete();

            DB::commit();

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus dan stok telah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('penjualan.index')->with('error', 'Gagal menghapus penjualan.');
        }
    }
}
