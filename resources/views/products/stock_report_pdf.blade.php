<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px 10px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 10px; text-align: right; color: #777; }
        
        .badge-success { color: green; font-weight: bold; }
        .badge-danger { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Stok Produk</h2>
        <p>Penjual: {{ Auth::user()->name ?? Auth::user()->nama ?? 'User' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-right">Harga (Rp)</th>
                <th class="text-center">Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
            @php
                // --- LOGIKA PERBAIKAN ---
                
                // 1. Ambil Kategori (LANGSUNG DARI KOLOM STRING)
                // Kita tidak perlu mengecek relasi ->name karena di Model Anda kategori adalah string.
                $namaKategori = $product->kategori ?? '-';

                // 2. Ambil Nama Produk (Prioritas: nama_barang -> nama -> name)
                $namaProduk = $product->nama_barang ?? $product->nama ?? $product->name ?? '-';

                // 3. Ambil Harga & Stok
                $hargaProduk = $product->harga ?? $product->price ?? 0;
                $stokProduk = $product->stok ?? $product->stock ?? 0;
            @endphp

            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $namaProduk }}</td>
                <td>{{ $namaKategori }}</td>
                <td class="text-right">{{ number_format($hargaProduk, 0, ',', '.') }}</td>
                <td class="text-center">{{ $stokProduk }}</td>
                <td>
                    @if($stokProduk > 0)
                        <span class="badge-success">Tersedia</span>
                    @else
                        <span class="badge-danger">Habis</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem FJB Tel-U
    </div>

</body>
</html>