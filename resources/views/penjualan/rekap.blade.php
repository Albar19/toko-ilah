<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Rekap Bulanan Penjualan</h2>
    </x-slot>

    <div class="p-4">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Bulan</th>
                    <th class="p-2 border">Jumlah Transaksi</th>
                    <th class="p-2 border">Total Penjualan</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekap as $r)
                <tr>
                    <td class="border p-2">{{ \Carbon\Carbon::parse($r->bulan)->translatedFormat('F Y') }}</td>
                    <td class="border p-2">{{ $r->jumlah_transaksi }}</td>
                    <td class="border p-2">Rp {{ number_format($r->total_penjualan, 0, ',', '.') }}</td>
                    <td class="border p-2">
                        <a href="{{ route('penjualan.rekap.detail', $r->bulan) }}" class="text-blue-600">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
