<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Method ini dijalankan ketika migration dieksekusi (php artisan migrate)
     */
    public function up()
    {
        // Membuat tabel 'products' untuk menyimpan data produk toko
        Schema::create('products', function (Blueprint $table) {
            $table->id();                               // Primary key auto increment (id)
            $table->string('nama_produk');              // Nama produk (varchar/string)
            $table->integer('harga');                   // Harga produk dalam rupiah (integer)
            $table->integer('stok')->default(0);       // Jumlah stok produk (default: 0)
            $table->string('image')->nullable();       // Nama file gambar produk (boleh kosong)
            $table->timestamps();                      // created_at dan updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'products' jika ada
        Schema::dropIfExists('products');
    }
};
