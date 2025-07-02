<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

// Mendefinisikan kelas komponen AppLayout yang mewarisi dari Component
class AppLayout extends Component
{
    /**
     * Mengambil view/konten yang merepresentasikan komponen ini.
     */
    public function render(): View
    {
        // Mengembalikan view 'layouts.app' sebagai tampilan utama layout aplikasi
        return view('layouts.app');
    }
}
