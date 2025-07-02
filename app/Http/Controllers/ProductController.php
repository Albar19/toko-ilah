<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ProductController
 * Controller untuk mengelola operasi CRUD (Create, Read, Update, Delete) produk
 * Menggunakan Resource Controller pattern Laravel
 */
class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk dengan pagination
     */
    public function index()
    {
        // Menggunakan pagination untuk membatasi 10 produk per halaman
        // Lebih efisien untuk performa jika data produk banyak
        $produk = Product::paginate(10);
        return view('produk.index', compact('produk'));
    }

    /**
     * Menampilkan form untuk membuat produk baru
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Menyimpan produk baru ke database
     * Mendukung upload gambar produk
     */
    public function store(Request $request)
    {
        // Validasi input dari user
        $request->validate([
            'nama_produk' => 'required',                                        // Nama produk wajib diisi
            'harga' => 'required|numeric',                                     // Harga wajib diisi dan berupa angka
            'stok' => 'required|numeric',                                      // Stok wajib diisi dan berupa angka
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Gambar opsional, max 2MB
        ]);

        $input = $request->all();  // Ambil semua input

        // Proses upload gambar jika ada
        if ($image = $request->file('image')) {
            $destinationPath = 'storage/products/';                           // Folder tujuan penyimpanan
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();  // Generate nama file unik
            $image->move($destinationPath, $profileImage);                    // Pindahkan file ke folder tujuan
            $input['image'] = "$profileImage";                                // Simpan nama file ke database
        }
    
        // Simpan data produk ke database
        Product::create($input);
     
        return redirect()->route('produk.index')->with('success','Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk tertentu (tidak digunakan)
     */
    public function show(Product $product)
    {
        // Method ini kosong, bisa digunakan untuk menampilkan detail produk
    }

    /**
     * Menampilkan form untuk edit produk
     */
    public function edit(Product $produk)
    {
        // Menggunakan route model binding, parameter $produk otomatis di-resolve
        return view('produk.edit', compact('produk'));
    }

    /**
     * Memperbarui data produk di database
     * Mendukung update gambar dengan mengganti gambar lama
     */
    public function update(Request $request, Product $produk)
    {
        // Validasi input yang sama seperti store
        $request->validate([
            'nama_produk' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $input = $request->all();

        // Proses upload gambar baru jika ada
        if ($image = $request->file('image')) {
            $destinationPath = 'storage/products/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = "$profileImage";
        } else {
            // Jika tidak ada gambar baru, hapus field image dari input
            // Sehingga gambar lama tetap dipertahankan
            unset($input['image']);
        }
    
        // Update data produk
        $produk->update($input);
    
        return redirect()->route('produk.index')->with('success','Produk berhasil diupdate.');
    }

    /**
     * Menghapus produk dari database
     */
    public function destroy(Product $produk)
    {
        // TODO: Sebaiknya ditambahkan penghapusan file gambar juga
        // if ($produk->image) {
        //     $imagePath = 'storage/products/' . $produk->image;
        //     if (file_exists($imagePath)) {
        //         unlink($imagePath);
        //     }
        // }
        
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
