<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forum Diskusi - FJB Tel-U</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-telu {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
        }
        .card-forum {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-forum:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
        }
        .btn-create-topic {
            background: linear-gradient(45deg, #b91d47, #d63384);
            border: none;
        }
        /* Style Tambahan untuk Widget API */
        .card-quote {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <div class="ms-auto">
                <a href="{{ route('home') }}" class="btn btn-outline-light rounded-pill btn-sm px-3 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </nav>

    <div class="bg-white border-bottom py-5 mb-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold text-dark mb-1">Forum Komunitas</h1>
                    <p class="text-muted m-0 lead">Ruang diskusi, tanya jawab, dan info seputar Telkom University.</p>
                </div>
                <a href="{{ route('forums.create') }}" class="btn btn-create-topic text-white rounded-pill px-4 py-3 fw-bold shadow">
                    <i class="bi bi-plus-lg me-2"></i> Buat Topik Baru
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">
            
            <div class="col-lg-8">
                @if(isset($forums) && $forums->count() > 0)
                    @foreach($forums as $forum)
                        <div class="card card-forum bg-white shadow-sm mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        {{ $forum->category }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> {{ $forum->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                
                                <h4 class="fw-bold mb-2">
                                    <a href="{{ route('forums.show', $forum->id) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $forum->title }}
                                    </a>
                                </h4>
                                
                                <p class="text-secondary text-truncate mb-4" style="max-width: 90%;">
                                    {{ Str::limit($forum->content, 120) }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-secondary"></i>
                                        </div>
                                        <div>
                                            <h6 class="m-0 fw-bold small text-dark">{{ $forum->user->name }}</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Mahasiswa</small>
                                        </div>
                                    </div>
                                    <div class="text-muted small fw-bold">
                                        <i class="bi bi-chat-dots-fill text-warning me-1"></i> {{ $forum->replies_count ?? 0 }} Balasan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-chat-square-text text-secondary opacity-25" style="font-size: 5rem;"></i>
                        <h4 class="mt-3 text-secondary fw-bold">Belum Ada Topik</h4>
                        <p class="text-muted">Jadilah yang pertama memulai diskusi di sini!</p>
                        <a href="{{ route('forums.create') }}" class="btn btn-outline-primary rounded-pill mt-2">
                            Mulai Diskusi
                        </a>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                
                @if(isset($quote))
                <div class="card card-quote border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden">
                    <i class="bi bi-quote position-absolute text-white opacity-25" style="font-size: 6rem; top: -20px; right: 10px;"></i>
                    
                    <div class="card-body p-4 position-relative">
                        <h6 class="fw-bold mb-3 text-white-50 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">
                            <i class="bi bi-stars me-1"></i> Quote of The Day
                        </h6>
                        
                        <figure class="mb-0">
                            <blockquote class="blockquote">
                                <p class="fs-5 fw-bold fst-italic">"{{ $quote }}"</p>
                            </blockquote>
                            <figcaption class="blockquote-footer text-white-50 mb-0 mt-2">
                                <cite title="Source Title" class="text-white">{{ $author ?? 'Unknown' }}</cite>
                            </figcaption>
                        </figure>
                    </div>
                </div>
                @endif
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-shield-exclamation me-2 text-danger"></i>Aturan Forum</h6>
                        <ul class="list-unstyled small text-secondary mb-0">
                            <li class="mb-2">1. Gunakan bahasa yang sopan & santun.</li>
                            <li class="mb-2">2. Dilarang spam atau promosi berlebihan.</li>
                            <li class="mb-2">3. Hormati privasi sesama mahasiswa.</li>
                            <li>4. Hindari topik SARA & Politik.</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Kategori Populer</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" class="btn btn-sm btn-light border rounded-pill">📢 Info Kampus</a>
                            <a href="#" class="btn btn-sm btn-light border rounded-pill">🛒 Jual Beli</a>
                            <a href="#" class="btn btn-sm btn-light border rounded-pill">🔍 Barang Hilang</a>
                            <a href="#" class="btn btn-sm btn-light border rounded-pill">💬 Curhat</a>
                            <a href="#" class="btn btn-sm btn-light border rounded-pill">⚽ UKM & Event</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>