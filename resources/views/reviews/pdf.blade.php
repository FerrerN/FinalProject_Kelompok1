<!DOCTYPE html>
<html>
<head>
    <title>Bukti Ulasan #{{ $review->id }}</title>
    <style>
        /* Mengatur font utama agar mendukung karakter unicode */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }
        .container {
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ffc107;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .product-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        /* CSS untuk Bintang */
        .rating {
            font-size: 30px; /* Ukuran bintang lebih besar */
            margin-bottom: 5px;
            color: #e0a800; /* Warna dasar */
            letter-spacing: 2px; /* Jarak antar bintang */
        }
        .star-full {
            color: #ffc107; /* Warna Emas untuk bintang penuh */
        }
        .star-empty {
            color: #ccc; /* Warna Abu-abu untuk bintang kosong */
        }
        
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            border-left: 5px solid #ffc107;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>BUKTI ULASAN PELANGGAN</h3>
            {{-- Teks "Toko Valendra" sudah dihapus dari sini --}}
        </div>

        <div style="text-align: center;">
            <p>Produk yang diulas:</p>
            <div class="product-name">{{ $review->product->nama_barang }}</div>
            
            {{-- Tampilan Bintang --}}
            <div class="rating">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                        {{-- Menggunakan HTML Entity untuk bintang penuh (★) --}}
                        <span class="star-full">&#9733;</span>
                    @else
                        {{-- Menggunakan HTML Entity untuk bintang kosong (☆) --}}
                        <span class="star-empty">&#9734;</span>
                    @endif
                @endfor
            </div>
            
            <div style="font-size: 14px; color: #555;">
                ({{ $review->rating }} dari 5 Bintang)
            </div>
        </div>

        <div class="content">
            <strong>Isi Komentar:</strong><br>
            "{{ $review->comment }}"
        </div>

        <div class="footer">
            <p>
                Dibuat oleh: {{ $review->user->name }}<br>
                Tanggal: {{ $review->created_at->format('d M Y, H:i') }} WIB<br>
                ID Transaksi: #{{ $review->transaction->id ?? '-' }}
            </p>
        </div>
    </div>
</body>
</html>