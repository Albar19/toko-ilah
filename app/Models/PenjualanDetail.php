<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model PenjualanDetail mewakili detail transaksi penjualan di database
class PenjualanDetail extends Model
{
    // Daftar atribut yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'penjualan_id',    // ID referensi ke tabel penjualan
        'product_id',      // ID referensi ke tabel produk
        'qty',             // Jumlah produk yang dijual
        'harga_satuan',    // Harga satuan produk
        'subtotal'         // Total harga untuk qty x harga_satuan
    ];

    // Relasi ke model Penjualan (setiap detail milik satu penjualan)
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    // Relasi ke model Product (setiap detail milik satu produk)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
