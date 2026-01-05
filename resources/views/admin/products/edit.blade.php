<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Produk - Admin FJB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold m-0">Edit Produk (Admin Mode)</h5>
                    <small class="text-muted">Pemilik asli: {{ $product->user->name }}</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="{{ $product->nama_barang }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <input type="text" name="kategori" class="form-control" value="{{ $product->kategori }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" value="{{ $product->harga }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" value="{{ $product->stok }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-danger fw-bold">Status Produk</label>
                                <select name="status" class="form-select border-danger">
                                    <option value="aktif" {{ $product->status == 'aktif' ? 'selected' : '' }}>Aktif (Tayang)</option>
                                    <option value="terjual" {{ $product->status == 'terjual' ? 'selected' : '' }}>Terjual (Sold)</option>
                                    <option value="ditarik" {{ $product->status == 'ditarik' ? 'selected' : '' }}>Ditarik (Banned/Hidden)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL Gambar</label>
                            <input type="url" name="url_gambar" class="form-control" value="{{ $product->url_gambar }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4">{{ $product->deskripsi }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products') }}" class="btn btn-light w-50">Batal</a>
                            <button type="submit" class="btn btn-primary w-50">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
