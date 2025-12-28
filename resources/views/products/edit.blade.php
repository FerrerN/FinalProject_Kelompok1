<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Edit Produk - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-warning text-dark fw-bold py-3 rounded-top-4">
                        Edit Produk: {{ $product->nama_barang }}
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" value="{{ $product->nama_barang }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="Elektronik" {{ $product->kategori == 'Elektronik' ? 'selected' : '' }}>Elektronik & Gadget</option>
                                        <option value="Fashion" {{ $product->kategori == 'Fashion' ? 'selected' : '' }}>Fashion & Aksesoris</option>
                                        <option value="Buku" {{ $product->kategori == 'Buku' ? 'selected' : '' }}>Buku & Alat Tulis</option>
                                        <option value="Jasa" {{ $product->kategori == 'Jasa' ? 'selected' : '' }}>Jasa & Keahlian</option>
                                        <option value="Lainnya" {{ $product->kategori == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" value="{{ $product->harga }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="{{ $product->stok }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Foto Produk</label>
                                <div class="mb-2">
                                    <img src="{{ $product->url_gambar }}" alt="Foto Lama" class="img-thumbnail" style="height: 100px;">
                                    <small class="text-muted d-block">*Foto saat ini</small>
                                </div>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                <div class="form-text">Biarkan kosong jika tidak ingin mengganti foto.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4" required>{{ $product->deskripsi }}</textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning fw-bold">Update Produk</button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>