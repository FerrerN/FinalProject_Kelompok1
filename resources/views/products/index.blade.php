<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Produk - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-telu { background: linear-gradient(to right, #b91d47, #ee395f); }
        .card-product { transition: transform 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .card-product:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        .img-wrapper { height: 200px; background: #eee; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .img-wrapper img { object-fit: cover; height: 100%; width: 100%; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white d-none d-md-block">
                    Halo, {{ Auth::user()->name }} 
                    <span class="badge bg-white text-danger ms-1 text-uppercase">{{ Auth::user()->role }}</span>
                </span>
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light rounded-pill">Home</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark m-0">Daftar Produk</h3>
            
            @if(Auth::user()->role === 'penjual')
                <a href="{{ route('products.create') }}" class="btn btn-danger rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Produk
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($products as $product)
                <div class="col">
                    <div class="card card-product h-100 bg-white">
                        <div class="img-wrapper">
                            @if($product->url_gambar)
                                <img src="{{ $product->url_gambar }}" alt="{{ $product->nama_barang }}">
                            @else
                                <div class="text-muted text-center"><i class="bi bi-image fs-1"></i><br>No Image</div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-truncate">{{ $product->nama_barang }}</h5>
                            <p class="text-danger fw-bold mb-1">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                            <small class="text-muted mb-3">Stok: {{ $product->stok }}</small>
                            
                            <div class="mt-auto d-grid gap-2">
                                @if(Auth::user()->role === 'penjual')
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm flex-fill text-white fw-bold">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Hapus produk ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('transactions.create') }}" class="btn btn-danger rounded-pill fw-bold">
                                        <i class="bi bi-cart-plus"></i> Beli Sekarang
                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>