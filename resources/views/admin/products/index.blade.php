<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Management - Admin FJB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #b91d47 0%, #ee395f 100%); color: white; }
        .nav-link { color: rgba(255,255,255,0.8); margin-bottom: 5px; border-radius: 5px; padding: 10px 15px; }
        .nav-link:hover, .nav-link.active { color: white; background-color: rgba(255,255,255,0.2); font-weight: bold; }
        .img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-4 d-none d-md-block fixed-top" style="bottom:0; z-index:100;">
            <h4 class="fw-bold mb-4"><i class="bi bi-shield-lock-fill me-2"></i> FJB Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}"><i class="bi bi-people me-2"></i> User Management</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.products') }}"><i class="bi bi-box-seam me-2"></i> Products</a></li>
                <li class="nav-item mt-5">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link bg-transparent border-0 text-start w-100 text-white"><i class="bi bi-box-arrow-left me-2"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="col-md-10 offset-md-2 p-0">
            <div class="bg-white border-bottom p-3 d-flex justify-content-between align-items-center sticky-top">
                <h5 class="m-0 fw-bold">Product Management</h5>
                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-danger rounded-pill px-3"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
            </div>

            <div class="p-4">
                @if(session('success')) <div class="alert alert-success alert-dismissible fade show">{{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-secondary text-uppercase small">
                                    <tr>
                                        <th class="ps-4">Produk</th>
                                        <th>Penjual</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $product->url_gambar }}" class="img-thumb me-3 border">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ Str::limit($product->nama_barang, 30) }}</div>
                                                    <small class="text-muted">{{ $product->kategori }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="bi bi-person-circle text-secondary me-1"></i> {{ $product->user->name }}
                                        </td>
                                        <td class="fw-bold">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                                        <td>{{ $product->stok }}</td>
                                        <td>
                                            @if($product->status == 'aktif') <span class="badge bg-success">Aktif</span>
                                            @elseif($product->status == 'terjual') <span class="badge bg-secondary">Terjual</span>
                                            @else <span class="badge bg-danger">Ditarik/Banned</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini secara permanen?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3 border-0">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
