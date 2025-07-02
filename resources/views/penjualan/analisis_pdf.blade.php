<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Produk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        h2 { margin-bottom: 0; }
        .section-title { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Laporan Analisis Produk</h2>
    <p>Bulan: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>

    <div class="section-title">📈 Produk Terlaris</div>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Total Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produkTerlaris as $p)
            <tr>
                <td>{{ $p->nama_produk }}</td>
                <td>{{ $p->total_terjual }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">Tidak ada data penjualan bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">📉 Produk Tidak Pernah Terjual</div>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produkTidakLaku as $p)
            <tr>
                <td>{{ $p->nama_produk }}</td>
                <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">Semua produk sudah pernah terjual bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
