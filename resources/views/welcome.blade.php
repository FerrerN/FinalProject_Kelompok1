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
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 80px; /* Jarak agar tidak tertutup Navbar Fixed */
        }

        /* Navbar Khas Tel-U */
        .navbar-telu {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
            backdrop-filter: blur(10px);
        }

        /* Styling Menu Navigasi */
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: 0.3s;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }
        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid white;
        }

        /* Card Produk */
        .product-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }

        .product-img-wrapper {
            height: 220px;
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
        .product-card:hover .product-img-wrapper img { transform: scale(1.05); }

        .out-of-stock-badge {
            position: absolute; top: 10px; right: 10px;
            background: rgba(0,0,0,0.7); color: white;
            padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;
        }

        /* Floating Alert */
        .floating-alert {
            position: fixed; top: 100px; right: 20px; z-index: 1060; min-width: 300px;
        }

        /* Gradient Merah untuk Hero Banner */
        .hero-gradient-red {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
        }
    </style>
</head>
<body>

   <nav class="navbar navbar-expand-lg navbar-dark navbar-telu fixed-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('forums.index') }}">
                            <i class="bi bi-chat-square-text me-1"></i> Forum
                        </a>
                    </li>

                    @auth
                        @if(Auth::user()->role == 'pembeli')
                        <li class="nav-item me-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative border-0 rounded-circle" style="width: 45px; height: 45px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-cart3 fs-5"></i>
                                @php $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity'); @endphp
                                @if($cartCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-light">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="btn btn-light dropdown-toggle text-danger bg-white px-3 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span>{{ Auth::user()->name }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-4 p-2" style="min-width: 220px;">
                                <li><h6 class="dropdown-header text-uppercase text-muted small fw-bold">Menu {{ Auth::user()->role }}</h6></li>

                                {{-- MENU KHUSUS ADMIN --}}
                                @if(Auth::user()->role == 'admin')
                                    <li>
                                        <a class="dropdown-item py-2 rounded-2 fw-bold text-danger" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i> Dashboard Admin
                                        </a>
                                    </li>
                                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('admin.users') }}"><i class="bi bi-people me-2"></i> Kelola User</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                {{-- MENU KHUSUS PENJUAL --}}
                                @if(Auth::user()->role == 'penjual')
                                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('products.index') }}"><i class="bi bi-box-seam me-2 text-danger"></i> Kelola Produk</a></li>
                                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('products.export_stock') }}"><i class="bi bi-file-earmark-pdf me-2 text-warning"></i> Laporan Stok</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                <li><a class="dropdown-item py-2 rounded-2" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear me-2 text-primary"></i> Edit Profil</a></li>

                                @if(Auth::user()->role == 'pembeli')
                                <li><a class="dropdown-item py-2 rounded-2" href="{{ route('transactions.index') }}"><i class="bi bi-receipt me-2 text-success"></i> Riwayat Transaksi</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 rounded-2 text-danger fw-bold">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-2">
                            <a href="{{ route('login') }}" class="btn btn-light text-danger fw-bold px-4 rounded-pill shadow-sm">Masuk</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-outline-light fw-bold px-4 rounded-pill">Daftar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success') || session('error'))
        <div class="floating-alert">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow border-0 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow border-0 rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    @endif

    <div class="container py-4">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 text-white hero-gradient-red">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1 class="fw-bold display-5 mb-3">Cari Barang Kampus? <br>Di Sini Aja!</h1>
                        <p class="lead mb-4 opacity-90">Platform jual beli aman, cepat, dan khusus untuk mahasiswa Telkom University. Temukan buku, gadget, hingga jasa.</p>
                        <div class="d-flex gap-3">
                            <a href="#katalog" class="btn btn-light text-danger fw-bold px-4 py-3 rounded-pill shadow-sm">
                                <i class="bi bi-bag-fill me-2"></i> Belanja
                            </a>
                            <a href="{{ route('forums.index') }}" class="btn btn-outline-light fw-bold px-4 py-3 rounded-pill border-2">
                                <i class="bi bi-people-fill me-2"></i> Gabung Forum
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 text-center d-none d-lg-block">
                        <i class="bi bi-cart-check-fill" style="font-size: 8rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="katalog" class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Rekomendasi Terbaru</h3>
                <p class="text-muted small m-0">Update stok real-time dari teman kampusmu.</p>
            </div>

            @auth @if(Auth::user()->role == 'penjual')
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm">
                    <i class="bi bi-plus-lg"></i> Jual Barang
                </a>
            @endif @endauth
        </div>

        @if(isset($products) && $products->count() > 0)
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm position-relative">

                            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                                <div class="product-img-wrapper">
                                    @if($product->stok <= 0)
                                        <div class="out-of-stock-badge">Habis</div>
                                    @endif
                                    <img src="{{ $product->url_gambar }}" alt="{{ $product->nama_barang }}">
                                </div>
                            </a>

                            <div class="card-body d-flex flex-column p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-shop me-1 text-danger"></i> {{ Str::limit($product->user->name, 10) }}
                                    </small>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border" style="font-size: 0.65rem;">
                                        {{ $product->kategori ?? 'Umum' }}
                                    </span>
                                </div>

                                <h6 class="card-title fw-bold text-dark text-truncate mb-1">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                                        {{ $product->nama_barang }}
                                    </a>
                                </h6>

                                <div class="mt-auto pt-3 border-top">
                                    <h5 class="fw-bold text-danger mb-3">Rp {{ number_format($product->harga, 0, ',', '.') }}</h5>

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
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">Login Beli</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <i class="bi bi-box-seam text-secondary opacity-25" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-secondary">Belum Ada Produk</h4>
                <p class="text-muted">Jadilah yang pertama berjualan di sini!</p>
            </div>
        @endif

        <div class="text-center mt-5 text-muted small">
            &copy; 2025 FJB Telkom University - Kelompok 1 SI4807
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
