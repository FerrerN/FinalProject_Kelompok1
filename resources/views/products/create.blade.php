<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tambah Produk - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-danger text-white fw-bold py-3 rounded-top-4">
                        Tambah Produk Baru
                    </div>
                    <div class="card-body p-4">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" placeholder="Contoh: Laptop Gaming" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Elektronik" {{ old('kategori') == 'Elektronik' ? 'selected' : '' }}>Elektronik & Gadget</option>
                                        <option value="Fashion" {{ old('kategori') == 'Fashion' ? 'selected' : '' }}>Fashion & Aksesoris</option>
                                        <option value="Buku" {{ old('kategori') == 'Buku' ? 'selected' : '' }}>Buku & Alat Tulis</option>
                                        <option value="Jasa" {{ old('kategori') == 'Jasa' ? 'selected' : '' }}>Jasa & Keahlian</option>
                                        <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}" placeholder="150000" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Stok Awal</label>
                                    <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok') }}" placeholder="10" min="1" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload Foto Produk</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" required>
                                <div class="form-text">Format: JPG, PNG. Maksimal 2MB.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Deskripsi Produk</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Jelaskan kondisi barang..." required>{{ old('deskripsi') }}</textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger btn-lg fw-bold">Simpan Produk</button>
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