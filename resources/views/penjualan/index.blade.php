<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Riwayat Penjualan</h2>
    </x-slot>

    <div class="p-4">
        {{-- ✅ Tombol Tambah Penjualan --}}
        <div class="mb-4">
            <a href="{{ route('penjualan.create') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                ➕ Tambah Penjualan
            </a>
        </div>

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
                    <td class="border p-2">
                        <a href="{{ route('penjualan.show', $jual->id) }}" class="text-blue-500 hover:underline">Detail</a>
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
