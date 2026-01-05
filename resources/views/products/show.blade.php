<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $product->nama_barang }} - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .img-product { width: 100%; height: 400px; object-fit: cover; border-radius: 12px; }
    </style>
</head>
<body class="bg-light">

    <div class="container py-5">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary mb-4 rounded-pill px-4 fw-bold">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Beranda
        </a>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="row g-0">
                        <div class="col-md-12 p-3 bg-white">
                            @if($product->url_gambar)
                                <img src="{{ $product->url_gambar }}" class="img-product" alt="{{ $product->nama_barang }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted img-product">
                                    <span>Tidak ada gambar</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-12">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                        {{ $product->kategori ?? 'Umum' }}
                                    </span>
                                    <small class="text-muted">
                                        Diupdate: {{ $product->updated_at->diffForHumans() }}
                                    </small>
                                </div>

                                <h2 class="fw-bold text-dark mb-2">{{ $product->nama_barang }}</h2>
                                
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <h3 class="text-danger fw-bold m-0">Rp {{ number_format($product->harga, 0, ',', '.') }}</h3>
                                    @if($product->stok <= 5)
                                        <span class="badge bg-danger text-white ms-2">Sisa {{ $product->stok }}!</span>
                                    @endif
                                </div>

                                <h6 class="fw-bold text-dark">Deskripsi Produk</h6>
                                <p class="text-secondary" style="white-space: pre-line;">{{ $product->deskripsi }}</p>

                                <hr class="my-4">

                                <div class="d-grid gap-2">
                                    @auth
                                        @if(Auth::id() !== $product->user_id)
                                            @if($product->stok > 0)
                                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-lg w-100 fw-bold rounded-pill">
                                                        <i class="bi bi-cart-plus me-2"></i> Masukkan Keranjang
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-lg w-100 fw-bold rounded-pill" disabled>
                                                    Stok Habis
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-lg w-100 fw-bold rounded-pill text-dark">
                                                <i class="bi bi-pencil-square me-2"></i> Edit Produk Ini
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger btn-lg w-100 fw-bold rounded-pill">
                                            Login untuk Membeli
                                        </a>
                                    @endauth
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-star-fill text-warning me-2"></i> Ulasan Pembeli ({{ $product->reviews->count() }})
                        </div>
                        
                        @if(isset($reviewableTransaction) && $reviewableTransaction)
                            <a href="{{ route('reviews.create', $reviewableTransaction->id) }}" class="btn btn-sm btn-warning fw-bold text-dark shadow-sm">
                                <i class="bi bi-pencil-fill me-1"></i> Tulis Ulasan
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        @forelse($product->reviews as $review)
                            <div class="mb-3 border-bottom pb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $review->user->name }}
                                            @if(Auth::check() && $review->user_id == Auth::id()) 
                                                <span class="badge bg-info bg-opacity-10 text-info border ms-2">Anda</span> 
                                            @endif
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            {{ $review->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    
                                    @if(Auth::check() && Auth::id() == $review->user_id)
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light rounded-circle text-muted" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu border-0 shadow dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('reviews.edit', $review->id) }}">
                                                        <i class="bi bi-pencil me-2 text-warning"></i> Edit Ulasan
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus ulasan ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-warning mb-2 mt-1">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                    <span class="text-muted ms-1 small">({{ $review->rating }}.0)</span>
                                </div>

                                <p class="mb-0 text-secondary bg-light p-3 rounded-3 fst-italic">
                                    "{{ $review->comment }}"
                                </p>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-chat-square-quote text-muted opacity-25" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Belum ada ulasan untuk produk ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted small">Tentang Penjual</h6>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-shop fs-3 text-danger"></i>
                        </div>
                        <div>
                            <h6 class="m-0 fw-bold text-dark">{{ $product->user->name }}</h6>
                            <small class="text-muted">{{ $product->user->email }}</small>
                            <div class="mt-1">
                                <span class="badge bg-success bg-opacity-10 text-success">Terpercaya</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted small">Informasi Stok</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Sisa Stok:</span>
                        <span class="fw-bold {{ $product->stok > 0 ? 'text-dark' : 'text-danger' }}">{{ $product->stok }} Unit</span>
                    </div>
                    <hr>
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span>Transaksi Aman dengan FJB Tel-U</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="bi bi-clock-history text-primary fs-5"></i>
                        <span>Respon Penjual Cepat</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>