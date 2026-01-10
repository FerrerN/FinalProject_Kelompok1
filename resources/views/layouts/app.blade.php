<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Judul Halaman Dinamis --}}
    <title>@yield('title', 'FJB Tel-U')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', sans-serif; 
            padding-top: 80px; /* Jarak untuk Navbar Fixed */
        }
        .navbar-telu { 
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%); 
            backdrop-filter: blur(10px);
        }
        .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover { color: #fff !important; transform: translateY(-2px); }
        
        /* Floating Alert */
        .floating-alert { position: fixed; top: 100px; right: 20px; z-index: 9999; min-width: 300px; }
    </style>
</head>
<body>

    {{-- NAVBAR UTAMA --}}
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('forums.index') }}"><i class="bi bi-chat-square-text me-1"></i> Forum</a></li>

                    @auth
                        {{-- Icon Keranjang (Pembeli) --}}
                        @if(Auth::user()->role == 'pembeli')
                        <li class="nav-item me-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative border-0 rounded-circle" style="width: 45px; height: 45px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-cart3 fs-5"></i>
                                @php $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('quantity'); @endphp
                                @if($cartCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-light">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif

                        {{-- Dropdown User --}}
                        <li class="nav-item dropdown">
                            <a class="btn btn-light dropdown-toggle text-danger bg-white px-3 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-4 p-2" style="min-width: 220px;">
                                <li><h6 class="dropdown-header text-uppercase text-muted small fw-bold">Menu {{ Auth::user()->role }}</h6></li>
                                
                                @if(Auth::user()->role == 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">Kelola User</a></li>
                                @endif

                                @if(Auth::user()->role == 'penjual')
                                    <li><a class="dropdown-item" href="{{ route('products.index') }}">Kelola Produk</a></li>
                                    <li><a class="dropdown-item" href="{{ route('transactions.index') }}">Pesanan Masuk</a></li>
                                @endif

                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profil</a></li>
                                
                                @if(Auth::user()->role == 'pembeli')
                                    <li><a class="dropdown-item" href="{{ route('transactions.index') }}">Riwayat Transaksi</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-2"><a href="{{ route('login') }}" class="btn btn-light text-danger fw-bold px-4 rounded-pill shadow-sm">Masuk</a></li>
                        <li class="nav-item"><a href="{{ route('register') }}" class="btn btn-outline-light fw-bold px-4 rounded-pill">Daftar</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- ALERT PESAN --}}
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

    {{-- KONTEN UTAMA --}}
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>