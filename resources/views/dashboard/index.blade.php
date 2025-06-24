<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Dashboard</h2>
    </x-slot>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow p-4 rounded-xl">
            <h3 class="text-gray-700 text-sm">Jumlah Produk</h3>
            <p class="text-2xl font-bold">{{ $jumlahProduk }}</p>
        </div>

        <div class="bg-white shadow p-4 rounded-xl">
            <h3 class="text-gray-700 text-sm">Transaksi Hari Ini</h3>
            <p class="text-2xl font-bold">{{ $transaksiHariIni }}</p>
        </div>

        <div class="bg-white shadow p-4 rounded-xl">
            <h3 class="text-gray-700 text-sm">Omset Hari Ini</h3>
            <p class="text-2xl font-bold">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white shadow p-4 rounded-xl">
            <h3 class="text-gray-700 text-sm">Omset Bulan Ini</h3>
            <p class="text-2xl font-bold">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white shadow p-4 rounded-xl col-span-2">
            <h3 class="text-gray-700 text-sm">Produk Terlaris Bulan Ini</h3>
            <p class="text-lg font-semibold">{{ $produkTerlarisNama }}</p>
            <p class="text-sm text-gray-600">Total terjual: {{ $produkTerlarisQty }} pcs</p>
        </div>
    </div>
</x-app-layout>
