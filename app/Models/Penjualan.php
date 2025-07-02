<?php

namespace App\Models; // Mendefinisikan namespace agar class mudah diatur dan diakses

use Illuminate\Database\Eloquent\Model; // Mengimpor class Model dari Eloquent ORM

class Penjualan extends Model // Mendefinisikan model Penjualan yang mewarisi Eloquent Model
{
    // Daftar atribut yang boleh diisi secara massal (mass assignment)
    protected $fillable = ['tanggal', 'total_harga'];

    // Relasi one-to-many: satu penjualan memiliki banyak detail penjualan
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
