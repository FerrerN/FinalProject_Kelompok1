<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FJB Tel-U - Jual Beli Mahasiswa</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            /* Background Putih Bersih */
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Gradient Merah Maroon Khas Tel-U */
        .hero-gradient {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
        }

        /* Card Produk */
        .product-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }

        /* Wrapper Gambar agar Ukuran Seragam */
        .product-img-wrapper {
            height: 200px;
            overflow: hidden;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .product-img-wrapper img {
            object-fit: cover;
            height: 100%;
            width: 100%;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img-wrapper img {
            transform: scale(1.05);
        }

        /* Badge Stok Habis */
        .out-of-stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="container mt-3 position-absolute start-50 translate-middle-x" style="z-index: 1050; max-width: 600px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <div class="container py-5">

        <div class="card border-0 shadow-lg mb-5 overflow-hidden text-white hero-gradient rounded-4">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">Selamat Datang di FJB Tel-U</h1>
                        <p class="lead mb-4 opacity-90">
                            Pusat jual beli terpercaya khusus mahasiswa Telkom University.<br>
                            Temukan Gadget, Fashion, hingga Jasa dengan harga mahasiswa.
                        </p>
                        
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            @auth
                                <a href="{{ route('cart.index') }}" class="btn btn-light text-success fw-bold px-4 py-3 rounded-pill shadow-sm position-relative">
                                    <i class="bi bi-cart3 fs-5 me-1"></i> Keranjang
                                    
                                    @php 
                                        $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity'); 
                                    @endphp
                                    
                                    @if($cartCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-outline-light fw-bold px-4 py-3 rounded-pill dropdown-toggle border-2" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-circle me-2"></i> Hai, {{ Auth::user()->name }}
                                    </button>
                                    <ul class="dropdown-menu shadow border-0 mt-2 rounded-3">
                                        <li><h6 class="dropdown-header text-uppercase text-muted small fw-bold">Role: {{ Auth::user()->role }}</h6></li>
                                        
                                        @if(Auth::user()->role == 'penjual')
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('products.index') }}">
                                                    <i class="bi bi-box-seam me-2 text-danger"></i> Kelola Produk Saya
                                                </a>
                                            </li>
                                        @endif
                                        
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('transactions.index') }}">
                                                <i class="bi bi-receipt me-2 text-primary"></i> Riwayat Transaksi
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger py-2 fw-bold">
                                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                            @else
                                <a href="{{ route('login') }}" class="btn btn-light text-danger fw-bold px-5 py-3 rounded-pill shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-light fw-bold px-5 py-3 rounded-pill border-2">
                                    Daftar Akun
                                </a>
                            @endauth
                        </div>
                    </div>
                    
                    <div class="col-lg-4 text-center d-none d-lg-block">
                        <i class="bi bi-bag-heart-fill" style="font-size: 10rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="katalog-produk" class="d-flex justify-content-between align-items-center mb-4 pt-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Rekomendasi Terbaru</h3>
                <p class="text-muted small m-0">Produk pilihan terbaik dari mahasiswa untuk mahasiswa</p>
            </div>
        </div>

        @if(isset($products) && $products->count() > 0)
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm">
                            
                            <div class="product-img-wrapper">
                                @if($product->stok <= 0)
                                    <div class="out-of-stock-badge">Habis</div>
                                @endif
                                
                                @if($product->url_gambar)
                                    <img src="{{ $product->url_gambar }}" alt="{{ $product->nama_barang }}">
                                @else
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image" style="font-size: 2.5rem;"></i>
                                        <p class="small m-0">No Image</p>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column p-3">
                                <small class="text-muted mb-2 d-flex align-items-center">
                                    <i class="bi bi-shop me-1 text-danger"></i> 
                                    {{ $product->user->name ?? 'Toko Mahasiswa' }}
                                </small>

                                <h6 class="card-title fw-bold text-dark text-truncate mb-1" title="{{ $product->nama_barang }}">
                                    {{ $product->nama_barang }}
                                </h6>

                                <div class="mb-2 text-warning" style="font-size: 0.8rem;">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                    <span class="text-muted ms-1">(5.0)</span>
                                </div>

                                <div class="mt-auto pt-3 border-top">
                                    <h5 class="fw-bold text-danger mb-3">
                                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                                    </h5>
                                    
                                    @auth
                                        @if(Auth::user()->id !== $product->user_id) 
                                            
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold btn-sm">
                                                    <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                                </button>
                                            </form>

                                        @else
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm">
                                                <i class="bi bi-pencil me-1"></i> Edit Produk
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">
                                            Login Beli
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <i class="bi bi-box-seam text-secondary opacity-50" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-dark fw-bold">Belum Ada Produk</h4>
                <p class="text-muted">Jadilah penjual pertama yang memasarkan produk di sini!</p>
                
                @auth
                    @if(Auth::user()->role == 'penjual')
                        <a href="{{ route('products.create') }}" class="btn btn-danger rounded-pill px-4 mt-2">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Produk Sekarang
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-danger rounded-pill px-4 mt-2">
                        Daftar Sebagai Penjual
                    </a>
                @endauth
            </div>
        @endif

        <div class="text-center mt-5 text-muted small opacity-75">
            &copy; 2025 FJB Telkom University - Web Application Development Team 1
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>