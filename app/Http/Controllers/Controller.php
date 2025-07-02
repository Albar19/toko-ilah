<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base Controller Class
 * Kelas controller dasar yang diwarisi oleh semua controller lain dalam aplikasi
 */
class Controller extends BaseController
{
    // Menggunakan trait untuk menambahkan fungsionalitas tambahan
    use AuthorizesRequests,    // Trait untuk otorisasi/permission (mengecek hak akses user)
        ValidatesRequests;     // Trait untuk validasi request (memvalidasi input dari user)
}
