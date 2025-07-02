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
        // Membuat tabel 'penjualan_details' untuk menyimpan detail item dalam setiap transaksi penjualan
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();                                                               // Primary key auto increment
            $table->foreignId('penjualan_id')->constrained()->onDelete('cascade');     // FK ke tabel penjualans (cascade delete)
            $table->foreignId('product_id')->constrained()->onDelete('cascade');       // FK ke tabel products (cascade delete)
            $table->integer('qty');                                                    // Kuantitas/jumlah produk yang dibeli
            $table->integer('harga_satuan');                                          // Harga per unit saat transaksi
            $table->integer('subtotal');                                              // Total harga (qty × harga_satuan)
            $table->timestamps();                                                      // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'penjualan_details' jika ada
        Schema::dropIfExists('penjualan_details');
    }
};
