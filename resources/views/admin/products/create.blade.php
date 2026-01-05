<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Produk Baru - Admin FJB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru</h5>
                    <small class="text-muted">Produk ini akan terdaftar atas nama: <strong>{{ Auth::user()->name }}</strong></small>
                </div>
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Laptop Gaming Bekas" required value="{{ old('nama_barang') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Kategori</label>
                                <input type="text" name="kategori" class="form-control" placeholder="Contoh: Elektronik" required value="{{ old('kategori') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Harga (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga" class="form-control" placeholder="0" required value="{{ old('harga') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" placeholder="1" required value="{{ old('stok') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">URL Gambar Produk</label>
                            <input type="url" name="url_gambar" class="form-control" placeholder="https://..." required value="{{ old('url_gambar') }}">
                            <div class="form-text">Gunakan link gambar langsung (contoh: dari Unsplash atau Imgur).</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Deskripsi Lengkap</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan kondisi barang, spesifikasi, dll.">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products') }}" class="btn btn-light w-50 py-2 fw-bold">Batal</a>
                            <button type="submit" class="btn btn-primary w-50 py-2 fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Produk
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
