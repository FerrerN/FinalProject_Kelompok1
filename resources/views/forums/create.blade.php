<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Topik Baru - FJB Tel-U</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 80px; /* Supaya tidak ketutup navbar */
        }
        /* Gradient Merah Khas Tel-U */
        .navbar-telu {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
        }
        .card-form {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .form-control:focus, .form-select:focus {
            border-color: #b91d47;
            box-shadow: 0 0 0 0.25rem rgba(185, 29, 71, 0.25);
        }
        .btn-submit {
            background: linear-gradient(45deg, #b91d47, #d63384);
            border: none;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(185, 29, 71, 0.3);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <div class="ms-auto">
                <a href="{{ route('forums.index') }}" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Forum
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-dark">Mulai Diskusi Baru</h3>
                    <p class="text-muted">Bagikan informasi atau tanyakan sesuatu kepada mahasiswa lain.</p>
                </div>

                <div class="card card-form bg-white">
                    <div class="card-body p-4 p-md-5">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('forums.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Judul Topik</label>
                                <input type="text" name="title" class="form-control form-control-lg" placeholder="Contoh: Info Kost Murah Dekat Kampus..." value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Kategori</label>
                                <select name="category" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Diskusi Umum" {{ old('category') == 'Diskusi Umum' ? 'selected' : '' }}>Diskusi Umum</option>
                                    <option value="Info Kampus" {{ old('category') == 'Info Kampus' ? 'selected' : '' }}>Info Kampus</option>
                                    <option value="Jual Beli" {{ old('category') == 'Jual Beli' ? 'selected' : '' }}>Jual Beli Cepat</option>
                                    <option value="Lost & Found" {{ old('category') == 'Lost & Found' ? 'selected' : '' }}>Barang Hilang/Temu</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Isi Diskusi</label>
                                <textarea name="content" class="form-control" rows="6" placeholder="Tuliskan detail diskusi, pertanyaan, atau informasi Anda di sini..." required>{{ old('content') }}</textarea>
                                <div class="form-text">Gunakan bahasa yang sopan dan jelas.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-submit text-white btn-lg fw-bold rounded-pill py-3">
                                    <i class="bi bi-send-fill me-2"></i> Posting Sekarang
                                </button>
                                <a href="{{ route('forums.index') }}" class="btn btn-light text-muted fw-bold rounded-pill py-3">
                                    Batal
                                </a>
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