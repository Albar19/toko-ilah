<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Riwayat Penjualan</h2>
    </x-slot>

    <div class="p-4">
        {{-- Tombol Tambah Penjualan --}}
        <div class="mb-4">
            <a href="{{ route('penjualan.create') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                ➕ Tambah Penjualan
            </a>
        </div>

        {{-- Notifikasi Sukses/Error --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        {{-- Tabel Penjualan --}}
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Tanggal</th>
                    <th class="border p-2">Total</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $jual)
                <tr>
                    <td class="border p-2">{{ $jual->tanggal }}</td>
                    <td class="border p-2">Rp {{ number_format($jual->total_harga, 0, ',', '.') }}</td>
                    <td class="border p-2 text-center">
                        <a href="{{ route('penjualan.show', $jual->id) }}" class="text-blue-500 hover:underline">Detail</a> |
                        <a href="{{ route('penjualan.edit', $jual->id) }}" class="text-yellow-600 hover:underline">Edit</a> |
                        <form action="{{ route('penjualan.destroy', $jual->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penjualan ini? Stok akan dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-4 text-center">Belum ada data penjualan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>