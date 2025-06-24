<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Penjualan</h2>
    </x-slot>

    <div class="p-4">
        <p><strong>Tanggal:</strong> {{ $penjualan->tanggal }}</p>
        <p><strong>Total:</strong> Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</p>

        <table class="w-full border mt-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Produk</th>
                    <th class="border p-2">Qty</th>
                    <th class="border p-2">Harga</th>
                    <th class="border p-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $item)
                <tr>
                    <td class="border p-2">{{ $item->product->nama_produk }}</td>
                    <td class="border p-2">{{ $item->qty }}</td>
                    <td class="border p-2">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="border p-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <a href="{{ route('penjualan.index') }}" class="text-gray-600">← Kembali ke riwayat</a>
        </div>
    </div>
</x-app-layout>
