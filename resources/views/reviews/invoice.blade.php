<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #444; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; font-weight: bold; }
        
        .total-section { float: right; margin-top: 20px; width: 40%; }
        .total-table { width: 100%; }
        .total-table td { padding: 5px; text-align: right; }
        .grand-total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>INVOICE PEMBELIAN</h1>
        <p>Toko Valendra | Jl. Raya Bandung No. 123</p>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <strong>Penerima:</strong><br>
                {{ $transaction->user->name }}<br>
                {{ $transaction->user->email }}<br>
            </td>
            <td style="text-align: right;">
                <strong>Detail Transaksi:</strong><br>
                No. Inv: #INV-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}<br>
                Tanggal: {{ $transaction->created_at->format('d M Y') }}<br>
                Status: {{ ucfirst($transaction->status) }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->product->nama_barang }}</td>
                <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                <td>{{ $detail->qty }}</td>
                <td>Rp {{ number_format($detail->price * $detail->qty, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table class="total-table">
            <tr>
                <td>Subtotal:</td>
                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pajak (0%):</td>
                <td>Rp 0</td>
            </tr>
            <tr>
                <td class="grand-total">Total Bayar:</td>
                <td class="grand-total">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di Toko Valendra.
    </div>

</body>
</html>