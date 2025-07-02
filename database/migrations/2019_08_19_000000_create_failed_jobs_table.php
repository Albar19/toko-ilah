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
    public function up(): void
    {
        // Membuat tabel 'failed_jobs' di database
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();                                    // Kolom ID sebagai primary key (auto increment)
            $table->string('uuid')->unique();               // UUID unik untuk setiap job yang gagal
            $table->text('connection');                     // Nama koneksi database yang digunakan
            $table->text('queue');                          // Nama queue tempat job dijalankan
            $table->longText('payload');                    // Data/payload dari job yang gagal (JSON)
            $table->longText('exception');                  // Detail error/exception yang menyebabkan job gagal
            $table->timestamp('failed_at')->useCurrent();  // Waktu kapan job gagal (default: waktu sekarang)
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'failed_jobs' jika ada
        Schema::dropIfExists('failed_jobs');
    }
};
