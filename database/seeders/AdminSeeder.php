<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Method ini dijalankan ketika seeder dieksekusi (php artisan db:seed)
     */

    public function run()
    {
        // Memasukkan data admin default ke tabel 'users'
        DB::table('users')->insert([
            'name' => 'Admin',                          // Nama admin
            'email' => 'admin@toko.com',                // Email/username untuk login
            'password' => Hash::make('admin123'),       // Password yang di-hash untuk keamanan
        ]);
    }
}
