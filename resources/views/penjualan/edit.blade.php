<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Penjualan - Toko Ilah</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $penjualan->tanggal) }}" class="w-full border rounded p-2">
            </div>

            <table class="w-full border mt-4" id="produk-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Produk</th>
                        <th class="p-2 border">Qty</th>
                        <th class="p-2 border">Harga</th>
                        <th class="p-2 border">Subtotal</th>
                        <th class="p-2 border">#</th>
                    </tr>
                </thead>
                <tbody id="produk-body">
                    @php
                        // Logika untuk menangani data lama (old) jika ada, jika tidak, gunakan data dari database
                        $details = old('produk_id') ? collect(old('produk_id'))->map(function($id, $index) use ($products) {
                            $product = $products->find($id);
                            $harga = $product ? $product->harga : 0;
                            $qty = (int)old('qty.'.$index, 1);
                            return (object)[
                                'product_id' => $id,
                                'qty' => $qty,
                                'harga_satuan' => $harga,
                                'subtotal' => $qty * $harga,
                            ];
                        }) : $penjualan->details;
                    @endphp

                    @foreach($details as $index => $detail)
                    <tr>
                        <td class="border p-2">
                            <select name="produk_id[]" class="product-dropdown w-full border p-2" onchange="updateHarga(this)">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-harga="{{ $product->harga }}" 
                                        {{ $detail->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2">
                            <input type="number" name="qty[]" value="{{ $detail->qty }}" min="1" class="qty-input w-full border p-2" oninput="updateSubtotal(this)">
                        </td>
                        <td class="border p-2">
                            <input type="number" name="harga[]" value="{{ $detail->harga_satuan }}" readonly class="harga-input w-full border p-2 bg-gray-100">
                        </td>
                        <td class="border p-2">
                            <input type="number" name="subtotal[]" value="{{ $detail->subtotal }}" readonly class="subtotal-input w-full border p-2 bg-gray-100">
                        </td>
                        <td class="border p-2 text-center">
                            <button type="button" onclick="hapusBaris(this)" class="text-red-500">🗑️</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="my-4">
                <button type="button" onclick="tambahBaris()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">+ Tambah Produk</button>
            </div>

            <div class="mb-4 text-right">
                <strong>Total: Rp <span id="total">{{ number_format($penjualan->total_harga, 0, ',', '.') }}</span></strong>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Penjualan</button>
            <a href="{{ route('penjualan.index') }}" class="ml-3 text-gray-600">Batal</a>
        </form>
    </div>

    <script>
        const products = @json($products->keyBy('id'));

        function updateHarga(select) {
            const productId = select.value;
            const harga = products[productId] ? products[productId].harga : 0;
            const row = select.closest('tr');
            row.querySelector('.harga-input').value = harga;
            updateSubtotal(row.querySelector('.qty-input'));
        }

        function updateSubtotal(input) {
            const row = input.closest('tr');
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const harga = parseInt(row.querySelector('.harga-input').value) || 0;
            const subtotal = qty * harga;
            row.querySelector('.subtotal-input').value = subtotal;

            hitungTotal();
        }

        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-input').forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('total').innerText = total.toLocaleString('id-ID');
        }

        function tambahBaris() {
            const tbody = document.getElementById('produk-body');
            const newRow = tbody.rows[0].cloneNode(true);

            newRow.querySelectorAll('input').forEach(input => {
                if (input.name === 'qty[]') {
                    input.value = '1';
                } else {
                    input.value = '';
                }
            });
            newRow.querySelector('select').selectedIndex = 0;

            tbody.appendChild(newRow);
            updateHarga(newRow.querySelector('select'));
        }

        function hapusBaris(button) {
            const tbody = document.getElementById('produk-body');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
                hitungTotal();
            } else {
                alert("Minimal satu produk harus ada.");
            }
        }
        
        // Inisialisasi total saat halaman dimuat
        document.addEventListener('DOMContentLoaded', hitungTotal);
    </script>
</x-app-layout>