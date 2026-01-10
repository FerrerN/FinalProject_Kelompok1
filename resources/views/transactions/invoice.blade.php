<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->id }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #b91d47; }
        .invoice-title { font-size: 20px; font-weight: bold; text-align: right; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-box { text-align: right; }
        .total-label { font-size: 14px; color: #666; }
        .total-amount { font-size: 24px; font-weight: bold; color: #b91d47; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        
        /* Agar tombol print hilang saat dicetak kertas */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #333; color: white; border: none; border-radius: 5px;">
            Cetak Invoice
        </button>
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer; background: #ddd; border: none; border-radius: 5px; margin-left: 10px;">
            Kembali
        </button>
    </div>

    <div class="header">
        <div class="logo">FJB Tel-U</div>
        <div class="invoice-title">
            INVOICE<br>
            <span style="font-size: 14px; color: #666;">#TRX-{{ $transaction->id }}</span>
        </div>
    </div>

    <div class="info-grid">
        <div>
            <strong>DITERBITKAN OLEH:</strong><br>
            {{ $transaction->product->user->name }} (Penjual)<br>
            {{ $transaction->product->user->email }}
        </div>
        <div style="text-align: right;">
            <strong>UNTUK:</strong><br>
            {{ $transaction->user->name }} (Pembeli)<br>
            {{ $transaction->user->email }}<br>
            <br>
            <strong>TANGGAL:</strong> {{ $transaction->created_at->format('d M Y') }}
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $transaction->product->nama_barang }}</td>
                <td>Rp {{ number_format($transaction->product->harga, 0, ',', '.') }}</td>
                <td>{{ $transaction->quantity }}</td>
                <td style="text-align: right;">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-label">TOTAL PEMBAYARAN</div>
        <div class="total-amount">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
        <div style="margin-top: 10px; font-weight: bold; color: {{ $transaction->status == 'selesai' ? 'green' : 'orange' }}; border: 1px solid #ddd; display: inline-block; padding: 5px 15px; border-radius: 5px;">
            STATUS: {{ strtoupper($transaction->status) }}
        </div>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di FJB Telkom University.<br>
        Ini adalah bukti transaksi sah yang diterbitkan oleh sistem.
    </div>

</body>
</html>