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
        // Membuat tabel 'personal_access_tokens' untuk Laravel Sanctum
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();                                    // Primary key auto increment
            $table->morphs('tokenable');                     // Polymorphic relation (tokenable_id, tokenable_type)
                                                            // Bisa untuk User, Admin, atau model lain
            $table->string('name');                          // Nama token (contoh: "API Token", "Mobile App")
            $table->string('token', 64)->unique();          // Hash token yang unik (64 karakter)
            $table->text('abilities')->nullable();          // Permission/scope token (JSON format)
            $table->timestamp('last_used_at')->nullable();  // Waktu terakhir token digunakan
            $table->timestamp('expires_at')->nullable();    // Waktu kedaluwarsa token (opsional)
            $table->timestamps();                           // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     * Method ini dijalankan ketika migration di-rollback (php artisan migrate:rollback)
     */
    public function down(): void
    {
        // Menghapus tabel 'personal_access_tokens' jika ada
        Schema::dropIfExists('personal_access_tokens');
    }
};
