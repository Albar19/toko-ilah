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

/**
 * PenjualanController
 * Controller untuk mengelola semua operasi penjualan
 * Meliputi CRUD, laporan, dan analisis penjualan
 */
class PenjualanController extends Controller
{
    /**
     * Menampilkan form untuk membuat penjualan baru
     */
    public function create()
    {
        $products = Product::all();  // Ambil semua produk untuk dropdown
        return view('penjualan.create', compact('products'));
    }

    /**
     * Menyimpan data penjualan baru ke database
     * Dengan validasi stok dan menggunakan database transaction
     */
    public function store(Request $request)
    {
        // 1. Validasi input dasar
        $request->validate([
            'tanggal' => 'required|date',
            'produk_id.*' => 'required|exists:products,id',  // Validasi setiap produk harus ada di database
            'qty.*' => 'required|integer|min:1',
        ]);

        // 2. Agregasi produk untuk menggabungkan item yang sama
        $produkData = [];
        $totalHarga = 0;

        foreach ($request->produk_id as $i => $id_produk) {
            $qty = (int)$request->qty[$i];
            
            // Jika produk sudah ada, tambahkan qty-nya
            if (isset($produkData[$id_produk])) {
                $produkData[$id_produk]['qty'] += $qty;
            } else {
                // Ambil data produk dari database
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
            // Cek apakah stok mencukupi
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
                // Simpan detail penjualan
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'product_id' => $id_produk,
                    'qty' => $data['qty'],
                    'harga_satuan' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);

                // 7. Kurangi stok produk secara atomik
                $product = Product::find($id_produk);
                $product->decrement('stok', $data['qty']);
            }

            DB::commit(); // Simpan semua perubahan

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penjualan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan daftar semua penjualan
     */
    public function index()
    {
        $penjualans = Penjualan::orderBy('tanggal', 'desc')->get();  // Urutkan dari yang terbaru
        return view('penjualan.index', compact('penjualans'));
    }

    /**
     * Menampilkan detail penjualan beserta produk yang dibeli
     */
    public function show($id)
    {
        // Eager loading untuk mengambil detail beserta produk
        $penjualan = Penjualan::with('details.product')->findOrFail($id);
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Menampilkan rekap penjualan per bulan
     */
    public function rekap()
    {
        // Query untuk mengelompokkan penjualan per bulan
        $rekap = Penjualan::select(
            DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),      // Format bulan
            DB::raw("COUNT(*) as jumlah_transaksi"),                // Hitung jumlah transaksi
            DB::raw("SUM(total_harga) as total_penjualan")          // Total omset
        )
        ->groupBy('bulan')
        ->orderBy('bulan', 'desc')
        ->get();

        return view('penjualan.rekap', compact('rekap'));
    }

    /**
     * Menampilkan detail rekap penjualan untuk bulan tertentu
     */
    public function rekapDetail($bulan)
    {
        // Filter penjualan berdasarkan bulan
        $penjualans = Penjualan::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])->get();

        return view('penjualan.rekap_detail', [
            'penjualans' => $penjualans,
            'bulan' => $bulan
        ]);
    }

    /**
     * Menampilkan analisis produk terlaris dan tidak laku
     */
    public function analisis(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));

        // Query untuk mencari produk terlaris di bulan tertentu
        $produkTerlaris = DB::table('penjualan_details')
            ->join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
            ->whereRaw("DATE_FORMAT(penjualans.tanggal, '%Y-%m') = ?", [$bulan])
            ->select('penjualan_details.product_id', DB::raw('SUM(qty) as total_terjual'))
            ->groupBy('penjualan_details.product_id')
            ->orderByDesc('total_terjual')
            ->get()
            ->map(function ($item) {
                // Ambil nama produk
                $item->nama_produk = \App\Models\Product::find($item->product_id)?->nama_produk ?? 'Tidak Diketahui';
                return $item;
            });

        // Cari produk yang tidak pernah terjual di bulan ini
        $produkTerjualIds = $produkTerlaris->pluck('product_id')->toArray();
        $produkTidakLaku = \App\Models\Product::whereNotIn('id', $produkTerjualIds)->get();

        // Data untuk chart/grafik
        $labels = $produkTerlaris->pluck('nama_produk');
        $data = $produkTerlaris->pluck('total_terjual');

        return view('penjualan.analisis', compact('produkTerlaris', 'produkTidakLaku', 'labels', 'data', 'bulan'));
    }

    /**
     * Generate PDF untuk laporan analisis
     */
    public function analisisPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        // Query sama seperti analisis()
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

        // Generate PDF menggunakan DomPDF
        $pdf = Pdf::loadView('penjualan.analisis_pdf', compact('produkTerlaris', 'produkTidakLaku', 'bulan'));

        return $pdf->download('analisis-produk-'.$bulan.'.pdf');
    }

    /**
     * Generate PDF untuk rekap bulanan
     */
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
     * Menampilkan form untuk mengedit penjualan
     */
    public function edit(Penjualan $penjualan)
    {
        // Load relasi details beserta product, dan ambil semua produk untuk dropdown
        $penjualan->load('details.product');
        $products = Product::all();

        return view('penjualan.edit', compact('penjualan', 'products'));
    }

    /**
     * Memperbarui data penjualan di database
     * Mengembalikan stok lama dan mengurangi stok baru
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

                 // Cek stok setelah dikembalikan
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
     * Menghapus data penjualan dan mengembalikan stok
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
