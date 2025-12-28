<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $forum->title }} - FJB Tel-U</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 80px; /* Jarak Navbar */
        }
        /* Gradient Merah Khas Tel-U */
        .navbar-telu {
            background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%);
        }
        .avatar-circle {
            width: 45px; height: 45px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #6c757d;
        }
        .reply-card {
            border-left: 4px solid #b91d47;
            background-color: #fff;
        }
        .badge-category {
            background-color: rgba(185, 29, 71, 0.1); 
            color: #b91d47;
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 20px;
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
            <div class="col-lg-9">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-category fw-bold">
                                {{ $forum->category }}
                            </span>

                            @if(Auth::id() == $forum->user_id)
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3">
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('forums.edit', $forum->id) }}">
                                                <i class="bi bi-pencil me-2 text-warning"></i> Edit Topik
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('forums.destroy', $forum->id) }}" method="POST" onsubmit="return confirm('Yakin hapus topik ini selamanya?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="bi bi-trash me-2"></i> Hapus Topik
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <h2 class="fw-bold text-dark mb-3">{{ $forum->title }}</h2>
                        
                        <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                            <div class="avatar-circle me-3">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <h6 class="m-0 fw-bold">{{ $forum->user->name }}</h6>
                                <small class="text-muted">Diposting {{ $forum->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        <div class="fs-5 text-secondary" style="white-space: pre-line; line-height: 1.8;">
                            {{ $forum->content }}
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4 mt-5">
                    <h5 class="fw-bold m-0"><i class="bi bi-chat-text-fill text-danger me-2"></i> Balasan ({{ $forum->replies->count() }})</h5>
                </div>

                @forelse($forum->replies as $reply)
                    <div class="card reply-card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-dark me-2">{{ $reply->user->name }}</span>
                                    @if($reply->user_id == $forum->user_id)
                                        <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 0.7rem;">Penulis</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 text-secondary">{{ $reply->content }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-chat-square-dots text-muted opacity-25" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada balasan. Jadilah yang pertama!</p>
                    </div>
                @endforelse

                <div class="card border-0 shadow-sm rounded-4 mt-4 mb-5">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Tulis Balasan Anda</h6>
                        <form action="{{ route('forums.reply', $forum->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea name="content" class="form-control bg-light border-0" rows="3" placeholder="Ketik tanggapan Anda di sini..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-send-fill me-2"></i> Kirim Balasan
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