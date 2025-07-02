<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait untuk factory model (pembuatan data dummy)
use Illuminate\Foundation\Auth\User as Authenticatable; // Kelas dasar untuk autentikasi user
use Illuminate\Notifications\Notifiable; // Trait untuk notifikasi user
use Laravel\Sanctum\HasApiTokens; // Trait untuk API token (Laravel Sanctum)

class User extends Authenticatable
{
    // Menggunakan trait untuk API token, factory, dan notifikasi
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang boleh diisi secara massal (mass assignment).
     * Biasanya digunakan saat create/update data user.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Atribut yang akan disembunyikan saat model diubah ke array/JSON.
     * Biasanya untuk keamanan, seperti password dan token.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang otomatis di-cast ke tipe data tertentu.
     * Contoh: email_verified_at jadi objek DateTime, password di-hash otomatis.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
