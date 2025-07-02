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
        // Membuat tabel 'users' di database
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                    // Kolom ID sebagai primary key (auto increment)
            $table->string('name');                          // Kolom nama user (varchar)
            $table->string('email')->unique();              // Kolom email yang harus unik (tidak boleh duplikat)
            $table->timestamp('email_verified_at')->nullable(); // Kolom waktu verifikasi email (boleh kosong)
            $table->string('password');                     // Kolom password yang sudah di-hash
            $table->rememberToken();                        // Kolom untuk fitur "Remember Me" saat login
            $table->timestamps();                           // Kolom created_at dan updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'users' jika ada
        Schema::dropIfExists('users');
    }
};
