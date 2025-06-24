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
     */
    

public function run()
{
    DB::table('users')->insert([
        'name' => 'Admin',
        'email' => 'admin@toko-ilah.com',
        'password' => Hash::make('admin123'),
    ]);
}
}
