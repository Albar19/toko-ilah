<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Toko-Ilah') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100" x-data="{ open: false }">
    
    {{-- 🔹 Mobile Top Bar --}}
    <div class="md:hidden bg-white shadow p-4 flex justify-between items-center">
        <a href="{{ route('dashboard') }}" class="text-lg font-bold">🛍️ Toko-Ilah</a>
        <button @click="open = !open" class="text-2xl focus:outline-none">
             <span x-show="!open">☰</span>
             <span x-show="open">✖</span>
        </button>
    </div>

    <div class="flex min-h-screen">

        {{-- 🔸 Sidebar (Desktop & Mobile Combined Logic) --}}
        
        <!-- Backdrop untuk Mobile -->
        <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>

        <!-- Konten Sidebar -->
        <aside 
            class="fixed top-0 left-0 w-64 bg-white h-full z-50 shadow-lg transform -translate-x-full transition-transform md:relative md:translate-x-0 md:shadow"
            :class="{'translate-x-0': open}"
        >
            <div class="p-4">
                <h2 class="text-xl font-bold mb-6 text-center">🛍️ Toko-Ilah</h2>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 @if(request()->routeIs('dashboard')) bg-gray-200 @endif">🏠 Dashboard</a>
                    <a href="{{ route('produk.index') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 @if(request()->routeIs('produk.*')) bg-gray-200 @endif">📦 Produk</a>
                    <a href="{{ route('penjualan.index') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 @if(request()->routeIs('penjualan.index')) bg-gray-200 @endif">🛒 Penjualan</a>
                    <a href="{{ route('penjualan.analisis') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 @if(request()->routeIs('penjualan.analisis')) bg-gray-200 @endif">📈 Analisis</a>
                    <a href="{{ url('/rekap') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-200 @if(request()->is('rekap')) bg-gray-200 @endif">🧾 Rekap</a>
                    
                    <div class="pt-4">
                        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Anda yakin ingin keluar?')">
                            @csrf
                            <button class="w-full flex items-center gap-3 p-2 rounded-lg text-red-600 hover:bg-red-100">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </aside>


        {{-- 🔸 Main Content --}}
        {{-- Perubahan utama ada di sini: class 'md:ml-64' dihapus --}}
        <main class="flex-1 p-6">
            @if (isset($header))
                <header class="bg-white shadow mb-6">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            
            <div class="max-w-7xl mx-auto">
                 {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>