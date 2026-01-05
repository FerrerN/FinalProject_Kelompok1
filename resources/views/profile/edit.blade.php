<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-header bg-white fw-bold py-3">Edit Biodata & Profil</div>
                    <div class="card-body p-4">
                        
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email (Tidak bisa diubah)</label>
                                <input type="email" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor WhatsApp / HP</label>
                                <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}" placeholder="0812...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat / Lokasi COD</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Contoh: Asrama Putra Gedung 4">{{ Auth::user()->address }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Bio Singkat</label>
                                <textarea name="bio" class="form-control" rows="3">{{ Auth::user()->bio }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>