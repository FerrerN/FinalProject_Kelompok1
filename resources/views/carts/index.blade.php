<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-telu { background: linear-gradient(to right, #b91d47, #ee395f); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}"><i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U</a>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light rounded-pill">Kembali Belanja</a>
        </div>
    </nav>

    <div class="container">
        <h3 class="fw-bold mb-4">Keranjang Belanja</h3>

        <div class="row">
            <div class="col-lg-8">
                @if($carts->count() > 0)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-0">
                            @foreach($carts as $cart)
                                <div class="d-flex align-items-center p-3 border-bottom">
                                    <img src="{{ $cart->product->url_gambar }}" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                                    
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $cart->product->nama_barang }}</h6>
                                        <small class="text-muted d-block">Harga: Rp {{ number_format($cart->product->harga, 0, ',', '.') }}</small>
                                        <small class="text-muted">Qty: <b>{{ $cart->quantity }}</b></small>
                                    </div>

                                    <div class="text-end me-3">
                                        <span class="fw-bold text-danger">Rp {{ number_format($cart->product->harga * $cart->quantity, 0, ',', '.') }}</span>
                                    </div>

                                    <form action="{{ route('cart.destroy', $cart->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-link text-secondary"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x display-1 text-muted"></i>
                        <p class="mt-3 text-muted">Keranjang masih kosong.</p>
                        <a href="{{ route('home') }}" class="btn btn-danger rounded-pill">Mulai Belanja</a>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3">Ringkasan Belanja</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Barang</span>
                        <span class="fw-bold">{{ $carts->sum('quantity') }} pcs</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total Bayar</span>
                        <span class="fw-bold fs-5 text-danger">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    @if($carts->count() > 0)
                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2">
                                <i class="bi bi-shield-lock me-2"></i> Checkout Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>