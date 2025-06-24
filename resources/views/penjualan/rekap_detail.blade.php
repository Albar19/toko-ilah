<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detail Penjualan Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h2>
    </x-slot>

    <div class="p-4">
        <div class="mb-4">
            <a href="{{ route('penjualan.rekap.pdf', $bulan) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                🧾 Export Rekap Bulan Ini ke PDF
            </a>
        </div>
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $jual)
                <tr>
                    <td class="border p-2">{{ $jual->tanggal }}</td>
                    <td class="border p-2">Rp {{ number_format($jual->total_harga, 0, ',', '.') }}</td>
                    <td class="border p-2">
                        <a href="{{ route('penjualan.show', $jual->id) }}" class="text-blue-600">Lihat</a>
                    </td>
                </tr>
                @endforeach
                @if($penjualans->isEmpty())
                    <tr>
                        <td colspan="3" class="p-4 text-center">Belum ada data penjualan di bulan ini.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="mt-4">
            <a href="{{ route('penjualan.rekap') }}" class="text-gray-600">← Kembali ke rekap</a>
        </div>
    </div>
</x-app-layout>
