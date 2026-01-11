<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Ulasan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-header bg-white fw-bold py-3 text-center">Edit Ulasan Anda</div>
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold">{{ $review->product->nama_barang ?? 'Nama Produk' }}</h5>
                        <p class="text-muted">Ubah penilaian Anda untuk produk ini.</p>
                        
                        <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Rating Bintang</label>
                                <select name="rating" class="form-select form-select-lg text-center fw-bold text-warning" required>
                                    <option value="5" {{ $review->rating == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (Sempurna)</option>
                                    <option value="4" {{ $review->rating == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (Bagus)</option>
                                    <option value="3" {{ $review->rating == 3 ? 'selected' : '' }}>⭐⭐⭐ (Cukup)</option>
                                    <option value="2" {{ $review->rating == 2 ? 'selected' : '' }}>⭐⭐ (Kurang)</option>
                                    <option value="1" {{ $review->rating == 1 ? 'selected' : '' }}>⭐ (Buruk)</option>
                                </select>
                            </div>
                            
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold">Komentar</label>
                                <textarea name="comment" class="form-control" rows="4" required>{{ $review->comment }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 fw-bold mb-3">Simpan Perubahan</button>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('products.show', $review->product_id) }}" class="btn btn-light text-muted">Batal</a>

                                {{-- TOMBOL EXPORT ULASAN (UPDATED) --}}
                                {{-- Mengarah ke route reviews.export_pdf --}}
                                <a href="{{ route('reviews.export_pdf', $review->id) }}" class="btn btn-outline-danger" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Export Ulasan
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>