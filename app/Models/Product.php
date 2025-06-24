<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['nama_produk', 'harga', 'stok'];

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
