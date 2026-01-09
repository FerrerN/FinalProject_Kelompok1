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
            padding-top: 80px; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-telu {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
            backdrop-filter: blur(10px);
        }
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: 0.3s;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }
        footer { margin-top: auto; }
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('forums.index') }}">Forum</a></li>

                    @auth
                        @if(Auth::user()->role == 'pembeli')
                        <li class="nav-item me-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-light rounded-circle" style="width: 45px; height: 45px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-cart3 fs-5"></i>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="btn btn-light dropdown-toggle text-danger bg-white px-3 rounded-pill fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i> <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-4 p-2">
                                <li><h6 class="dropdown-header text-uppercase text-muted small fw-bold">Menu {{ Auth::user()->role }}</h6></li>
                                
                                @if(Auth::user()->role == 'penjual')
                                    <li><a class="dropdown-item py-2" href="{{ route('products.index') }}"><i class="bi bi-box-seam me-2 text-danger"></i> Kelola Produk</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('transactions.index') }}"><i class="bi bi-receipt me-2 text-primary"></i> Pesanan Masuk</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('products.export_stock') }}"><i class="bi bi-file-earmark-pdf me-2 text-warning"></i> Laporan Stok</a></li>
                                @endif

                                @if(Auth::user()->role == 'pembeli')
                                    <li><a class="dropdown-item py-2" href="{{ route('transactions.index') }}"><i class="bi bi-receipt me-2 text-success"></i> Riwayat Transaksi</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger fw-bold">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-2"><a href="{{ route('login') }}" class="btn btn-light text-danger fw-bold px-4 rounded-pill">Masuk</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="text-center py-4 text-muted small mt-5">
        &copy; 2025 FJB Telkom University
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>