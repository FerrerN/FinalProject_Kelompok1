<!DOCTYPE html>
<html lang="id">
<head>
    <title>{{ $forum->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="{{ route('forums.index') }}" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Kembali ke Forum</a>
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <span class="badge bg-primary mb-2">{{ $forum->category }}</span>
                <h2 class="fw-bold text-dark">{{ $forum->title }}</h2>
                <div class="d-flex align-items-center mb-3 text-muted small">
                    <span class="fw-bold me-2">{{ $forum->user->name }}</span> • 
                    <span class="ms-2">{{ $forum->created_at->format('d M Y, H:i') }}</span>
                </div>
                <hr>
                <p class="fs-5" style="white-space: pre-wrap;">{{ $forum->content }}</p>
            </div>
        </div>

        <h5 class="fw-bold mb-3">Balasan ({{ $forum->replies->count() }})</h5>
        
        @foreach($forum->replies as $reply)
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold text-primary">{{ $reply->user->name }}</h6>
                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 mt-1">{{ $reply->content }}</p>
                </div>
            </div>
        @endforeach

        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4 bg-white">
                <h6 class="fw-bold mb-3">Tulis Balasan</h6>
                <form action="{{ route('forums.reply', $forum->id) }}" method="POST">
                    @csrf
                    <textarea name="content" class="form-control mb-3" rows="3" placeholder="Tulis tanggapan Anda..." required></textarea>
                    <button type="submit" class="btn btn-dark px-4 fw-bold">Kirim Balasan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>