<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Analisis Produk</h2>
    </x-slot>

    <div class="p-4">
        {{-- Tombol Export PDF --}}
        <div class="mb-4">
            <a href="{{ route('penjualan.analisis.pdf', ['bulan' => $bulan]) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                🧾 Export PDF Analisis Bulan Ini
            </a>
        </div>

        {{-- Form Filter Bulan --}}
        <div class="mb-4">
            <form method="GET" action="{{ route('penjualan.analisis') }}" class="flex items-center gap-2">
                <label for="bulan" class="font-semibold">Pilih Bulan:</label>
                <input type="month" name="bulan" id="bulan" value="{{ $bulan }}" class="border p-2 rounded">
                <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Tampilkan</button>
            </form>
        </div>

        {{-- Grafik Produk Terlaris --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">
                📊 Grafik Produk Terlaris 
                <span class="text-sm font-normal text-gray-500">
                    ({{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }})
                </span>
            </h3>
            <canvas id="chartTerlaris" height="100"></canvas>
        </div>

        {{-- Tabel Produk Terlaris --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">📈 Produk Terlaris</h3>
            <table class="w-full border">
                <thead class="bg-green-100">
                    <tr>
                        <th class="p-2 border">Produk</th>
                        <th class="p-2 border">Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkTerlaris as $p)
                    <tr>
                        <td class="border p-2">{{ $p->nama_produk }}</td>
                        <td class="border p-2">{{ $p->total_terjual }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-4 text-center">Belum ada data penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tabel Produk Tidak Pernah Terjual --}}
        <div>
            <h3 class="text-lg font-semibold mb-2">📉 Produk Tidak Pernah Terjual</h3>
            <table class="w-full border">
                <thead class="bg-red-100">
                    <tr>
                        <th class="p-2 border">Produk</th>
                        <th class="p-2 border">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkTidakLaku as $p)
                    <tr>
                        <td class="border p-2">{{ $p->nama_produk }}</td>
                        <td class="border p-2">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-4 text-center">Semua produk sudah pernah terjual.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js CDN + Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartTerlaris').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Total Terjual',
                    data: {!! json_encode($data) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>
