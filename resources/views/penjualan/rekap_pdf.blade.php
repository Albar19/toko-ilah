<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Bulanan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        .total { font-weight: bold; background: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Rekap Penjualan Bulan: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualans as $jual)
            <tr>
                <td>{{ $jual->tanggal }}</td>
                <td>Rp {{ number_format($jual->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">Tidak ada transaksi pada bulan ini.</td>
            </tr>
            @endforelse
            @if($penjualans->count() > 0)
            <tr class="total">
                <td>Total Keseluruhan</td>
                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
