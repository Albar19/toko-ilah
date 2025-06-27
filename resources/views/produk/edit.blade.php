<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Produk</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="w-full border rounded p-2" value="{{ $produk->nama_produk }}" required>
            </div>

            <div class="mb-4">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" class="w-full border rounded p-2" value="{{ $produk->harga }}" required>
            </div>

            <div class="mb-4">
                <label>Stok</label>
                <input type="number" name="stok" class="w-full border rounded p-2" value="{{ $produk->stok }}" required>
            </div>

            <div class="mb-4">
                <label>Gambar Produk</label>
                <input type="file" name="image" class="w-full border rounded p-2">
                @if($produk->image)
                    <div class="mt-2">
                        <img src="{{ url('storage/products/'.$produk->image) }}" alt="{{ $produk->nama_produk }}" style="height: 100px; object-fit: cover;">
                        <small>Gambar saat ini</small>
                    </div>
                @endif
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
            <a href="{{ route('produk.index') }}" class="ml-2 text-gray-600">Kembali</a>
        </form>
    </div>
</x-app-layout>
