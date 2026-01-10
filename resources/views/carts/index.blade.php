<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - FJB Tel-U</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 80px;
        }
        /* Navbar Gradient Merah Khas Tel-U */
        .navbar-telu {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
        }
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        .card-cart {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <div class="ms-auto">
                <a href="{{ route('products.index') }}" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali Belanja
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h3 class="fw-bold mb-4">Keranjang Belanja</h3>

        <div class="row">
            <div class="col-lg-8 mb-4">
                @if($carts->isEmpty())
                    <div class="card card-cart rounded-4 p-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Keranjang Anda masih kosong.</h5>
                        <div class="mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-danger rounded-pill px-4">
                                Mulai Belanja
                            </a>
                        </div>
                    </div>
                @else
                    @foreach($carts as $cart)
                        <div class="card card-cart rounded-4 mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $cart->product->url_gambar ?? 'https://via.placeholder.com/80' }}" class="product-img me-3" alt="Produk">
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $cart->product->nama_barang }}</h6>
                                        <p class="text-danger fw-bold mb-0">Rp {{ number_format($cart->product->harga, 0, ',', '.') }}</p>
                                        <small class="text-muted">Qty: {{ $cart->quantity }}</small>
                                    </div>

                                    <form action="{{ route('cart.destroy', $cart->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-light text-danger btn-sm rounded-circle p-2" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3">Ringkasan Belanja</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Barang</span>
                        <span class="fw-bold">{{ $carts->sum('quantity') }} pcs</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Bayar</span>
                        <span class="fw-bold text-danger fs-5">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">
                            <i class="bi bi-truck me-1"></i> Estimasi Pengiriman
                        </label>
                        
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border">
                            <span class="fw-bold text-dark">
                                {{ $estimasi->isoFormat('dddd, D MMMM Y') }}
                            </span>
                            <span class="badge bg-primary">Reguler</span>
                        </div>

                        @if($infoLibur)
                            <div class="alert alert-warning mt-2 mb-0 py-2 small border-warning fade show">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                    <div>
                                        <strong>Perhatian:</strong> Tanggal pengiriman jatuh pada hari libur nasional: 
                                        <u class="fw-bold">{{ $infoLibur }}</u>. 
                                        Pengiriman mungkin tertunda.
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-success small mt-1">
                                <i class="bi bi-check-circle-fill me-1"></i> Jadwal pengiriman tersedia.
                            </div>
                        @endif
                    </div>
                    <form action="{{ route('checkout.process') }}" method="POST"> 
                        @csrf
                        <input type="hidden" name="shipping_date" value="{{ $estimasi->format('Y-m-d') }}">
                        
                        <button type="submit" class="btn btn-danger w-100 py-3 fw-bold rounded-pill shadow-sm" 
                            {{ $carts->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-lock-fill me-2"></i> Checkout Sekarang
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>