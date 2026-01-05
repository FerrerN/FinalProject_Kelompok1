<!DOCTYPE html>
<html lang="id">
<head>
    <title>Beri Ulasan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-header bg-white fw-bold py-3 text-center">Beri Ulasan Produk</div>
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold">{{ $transaction->product->nama_barang }}</h5>
                        <p class="text-muted">Bagaimana pengalamanmu belanja barang ini?</p>
                        
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $transaction->product_id }}">
                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Rating Bintang</label>
                                <select name="rating" class="form-select form-select-lg text-center fw-bold text-warning" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (Sempurna)</option>
                                    <option value="4">⭐⭐⭐⭐ (Bagus)</option>
                                    <option value="3">⭐⭐⭐ (Cukup)</option>
                                    <option value="2">⭐⭐ (Kurang)</option>
                                    <option value="1">⭐ (Buruk)</option>
                                </select>
                            </div>
                            
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold">Komentar</label>
                                <textarea name="comment" class="form-control" rows="4" placeholder="Ceritakan detail barangnya..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 fw-bold">Kirim Ulasan</button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-link text-muted mt-2">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>