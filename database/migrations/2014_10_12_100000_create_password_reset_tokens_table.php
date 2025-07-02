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
        // Membuat tabel 'password_reset_tokens' di database
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();         // Kolom email sebagai primary key (unik)
            $table->string('token');                     // Kolom token untuk reset password (random string)
            $table->timestamp('created_at')->nullable(); // Kolom waktu pembuatan token (boleh kosong)
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'password_reset_tokens' jika ada
        Schema::dropIfExists('password_reset_tokens');
    }
};
