<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelompok 1 - E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1556742049-0cfed4f7a07d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 80vh; /* Tinggi layar 80% */
            display: flex;
            align-items: center;
            color: white;
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-shop me-2"></i>Toko Kelompok 1
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Keunggulan</a>
                    </li>
                    
                    @auth
                        <li class="nav-item ms-lg-3">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('transactions.index') }}">Dashboard Transaksi</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Masuk</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Belanja Mudah & Aman</h1>
            <p class="lead mb-5 px-5">Temukan produk elektronik terbaik dengan harga mahasiswa. <br> Sistem transaksi cepat, transparan, dan terpercaya.</p>
            
            @auth
                <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow">
                    <i class="bi bi-cart-check me-2"></i> Mulai Belanja
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login Sekarang
                </a>
            @endauth
        </div>
    </header>

    <section id="features" class="py-5 bg-light">
        <div class="container py-5">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="mb-3"><i class="bi bi-lightning-charge feature-icon"></i></div>
                        <h4 class="card-title">Proses Cepat</h4>
                        <p class="card-text text-muted">Transaksi diproses secara realtime menggunakan teknologi Laravel terbaru.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="mb-3"><i class="bi bi-shield-lock feature-icon"></i></div>
                        <h4 class="card-title">Keamanan Terjamin</h4>
                        <p class="card-text text-muted">Data Anda dilindungi dengan sistem enkripsi modern dan validasi ketat.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="mb-3"><i class="bi bi-phone feature-icon"></i></div>
                        <h4 class="card-title">Responsif</h4>
                        <p class="card-text text-muted">Akses toko kami dari Laptop, Tablet, maupun Smartphone dengan nyaman.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <small>&copy; 2025 Kelompok 1 - Web Application Development. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>