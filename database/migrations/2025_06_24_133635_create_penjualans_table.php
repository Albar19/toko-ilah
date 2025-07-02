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
        // Membuat tabel 'penjualans' untuk menyimpan header/master data transaksi penjualan
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();                                   // Primary key auto increment (ID transaksi)
            $table->date('tanggal')->default(now());       // Tanggal transaksi (default: hari ini)
            $table->integer('total_harga')->default(0);    // Total harga keseluruhan transaksi (default: 0)
            $table->timestamps();                          // created_at dan updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'penjualans' jika ada
        Schema::dropIfExists('penjualans');
    }
};
