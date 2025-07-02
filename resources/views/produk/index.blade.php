<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Daftar Produk</h2>
    </x-slot>

    <div class="p-4">
        <a href="{{ route('produk.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">+ Tambah Produk</a>

        @if(session('success'))
            <div class="mt-4 text-green-600">{{ session('success') }}</div>
        @endif

        <div class="mt-4">
            <table class="w-full border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 border">No</th>
                        <th class="p-2 border">Gambar</th>
                        <th class="p-2 border">Nama Produk</th>
                        <th class="p-2 border">Harga</th>
                        <th class="p-2 border">Stok</th>
                        <th class="p-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produk as $index => $item)
                    <tr>
                        {{-- Penomoran sesuai paginasi --}}
                        <td class="p-2 border">{{ $produk->firstItem() + $index }}</td>
                        <td class="p-2 border">
                            @if($item->image)
                                <img src="{{ url('storage/products/'.$item->image) }}" style="height: 50px; width: 50px; object-fit: cover;"/>
                            @else
                                <span>No Image</span>
                            @endif
                        </td>
                        <td class="p-2 border">{{ $item->nama_produk }}</td>
                        <td class="p-2 border">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="p-2 border">{{ $item->stok }}</td>
                        <td class="p-2 border">
                            <a href="{{ route('produk.edit', $item->id) }}" class="text-blue-600">Edit</a> |
                            <form action="{{ route('produk.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($produk->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center p-4">Belum ada produk.</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $produk->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
