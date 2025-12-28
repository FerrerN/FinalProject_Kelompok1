<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Edit Topik - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-warning text-dark fw-bold py-3 rounded-top-4">
                        Edit Topik Diskusi
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('forums.update', $forum->id) }}" method="POST">
                            @csrf
                            @method('PUT') <div class="mb-3">
                                <label class="form-label fw-bold">Judul Topik</label>
                                <input type="text" name="title" class="form-control" value="{{ $forum->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="category" class="form-select" required>
                                    <option value="Diskusi Umum" {{ $forum->category == 'Diskusi Umum' ? 'selected' : '' }}>Diskusi Umum</option>
                                    <option value="Info Kampus" {{ $forum->category == 'Info Kampus' ? 'selected' : '' }}>Info Kampus</option>
                                    <option value="Jual Beli" {{ $forum->category == 'Jual Beli' ? 'selected' : '' }}>Jual Beli Cepat</option>
                                    <option value="Lost & Found" {{ $forum->category == 'Lost & Found' ? 'selected' : '' }}>Barang Hilang/Temu</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Isi Diskusi</label>
                                <textarea name="content" class="form-control" rows="6" required>{{ $forum->content }}</textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                                <a href="{{ route('forums.show', $forum->id) }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>