<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah Produk</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label>Stok</label>
                <input type="number" name="stok" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label>Gambar Produk</label>
                <input type="file" name="image" class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
            <a href="{{ route('produk.index') }}" class="ml-2 text-gray-600">Kembali</a>
        </form>
    </div>
</x-app-layout>
