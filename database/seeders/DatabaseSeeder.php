<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Method ini adalah entry point utama untuk menjalankan semua seeder
     * Dijalankan dengan perintah: php artisan db:seed
     */
    public function run(): void
    {
        // Memanggil AdminSeeder agar user admin dibuat
        // $this->call() digunakan untuk menjalankan seeder lain secara berurutan
        $this->call([
            AdminSeeder::class,  // Menjalankan AdminSeeder untuk membuat akun admin default
        ]);
    }
}
