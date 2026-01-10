<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FJB Tel-U') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 80px; /* Jarak untuk Navbar Fixed */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar Khas Tel-U (Gradien Merah) */
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
        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid white;
        }
        
        /* Dropdown Item Hover */
        .dropdown-item:active, .dropdown-item.active {
            background-color: #b91d47;
        }
    </style>
</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-dark navbar-telu fixed-top shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}">
                    <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Produk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('forums.*') ? 'active' : '' }}" href="{{ route('forums.index') }}">Forum</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        @guest
                            <li class="nav-item">
                                <a class="btn btn-light text-danger fw-bold rounded-pill px-4" href="{{ route('login') }}">Masuk</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-light fw-bold rounded-pill px-4" href="{{ route('register') }}">Daftar</a>
                            </li>
                        @else
                            {{-- Icon Keranjang (Khusus Pembeli) --}}
                            @if(Auth::user()->role == 'pembeli')
                                <li class="nav-item me-2">
                                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative border-0 rounded-circle" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-cart3 fs-5"></i>
                                        @php 
                                            // Hitung jumlah item di keranjang (Menggunakan Model Cart)
                                            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity'); 
                                        @endphp
                                        @if($cartCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-light">
                                                {{ $cartCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            {{-- Dropdown Profil User --}}
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="btn btn-light text-danger bg-white px-3 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle fs-5"></i> {{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-4 p-2" style="min-width: 220px;">
                                    <li><h6 class="dropdown-header text-uppercase text-muted small fw-bold">Menu {{ ucfirst(Auth::user()->role) }}</h6></li>
                                    
                                    {{-- Menu Admin --}}
                                    @if(Auth::user()->role == 'admin')
                                        <li><a class="dropdown-item rounded-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                        <li><a class="dropdown-item rounded-2" href="{{ route('admin.users') }}"><i class="bi bi-people me-2"></i> Kelola User</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif

                                    {{-- Menu Penjual --}}
                                    @if(Auth::user()->role == 'penjual')
                                        <li><a class="dropdown-item rounded-2" href="{{ route('products.index') }}"><i class="bi bi-box-seam me-2"></i> Kelola Produk</a></li>
                                        <li><a class="dropdown-item rounded-2" href="{{ route('transactions.index') }}"><i class="bi bi-bag-check me-2 text-success"></i> Pesanan Masuk</a></li>
                                        <li><a class="dropdown-item rounded-2" href="{{ route('products.export_stock') }}"><i class="bi bi-file-earmark-pdf me-2"></i> Laporan Stok</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif

                                    {{-- Menu Pembeli --}}
                                    @if(Auth::user()->role == 'pembeli')
                                        <li><a class="dropdown-item rounded-2" href="{{ route('transactions.index') }}"><i class="bi bi-receipt me-2 text-primary"></i> Riwayat Belanja</a></li>
                                    @endif

                                    <li><a class="dropdown-item rounded-2" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i> Edit Profil</a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded-2 text-danger fw-bold">
                                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-4">
            <div class="container">
                <!-- Global Flash Message -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Content Injection -->
                @yield('content')
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="text-center py-4 text-muted small mt-auto border-top">
            <div class="container">
                &copy; {{ date('Y') }} <strong>FJB Tel-U</strong>. Platform Jual Beli Mahasiswa Telkom University.
                <br>Dibuat oleh Kelompok 1 SI4807.
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>