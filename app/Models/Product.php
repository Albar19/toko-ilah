<?php

namespace App\Models; // Mendefinisikan namespace agar class mudah diatur dan di-autoload

use Illuminate\Database\Eloquent\Model; // Mengimpor class Model dari Eloquent ORM

class Product extends Model // Mendefinisikan class Product yang mewarisi Eloquent Model
{
    // Daftar atribut yang boleh diisi secara massal (mass assignment)
    protected $fillable = ['nama_produk', 'harga', 'stok', 'image'];

    // Relasi one-to-many: Satu produk bisa punya banyak detail penjualan
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
