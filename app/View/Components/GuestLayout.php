<?php

namespace App\View\Components; // Mendefinisikan namespace agar class mudah diorganisir dan diakses.

use Illuminate\View\Component; // Mengimpor class Component dari Laravel.
use Illuminate\View\View; // Mengimpor class View dari Laravel.

class GuestLayout extends Component // Mendefinisikan class GuestLayout yang mewarisi Component.
{
    /**
     * Mengambil view/konten yang merepresentasikan komponen ini.
     */
    public function render(): View // Fungsi render wajib ada pada komponen Laravel.
    {
        // Mengembalikan view 'layouts.guest' sebagai tampilan komponen.
        return view('layouts.guest');
    }
}
