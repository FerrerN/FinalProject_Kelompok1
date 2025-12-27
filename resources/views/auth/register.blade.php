<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            /* Background Putih Bersih */
            background-color: #f8f9fa; 
            min-height: 100vh;
        }
        .card-register {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff;
            box-shadow: none;
            border-color: #dc3545; /* Merah saat diklik */
        }
        .input-group-text {
            border-color: #e9ecef;
            background-color: #fff;
        }
        .btn-telu {
            background: linear-gradient(to right, #b91d47, #ee395f); /* Warna Tel-U */
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-telu:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(185, 29, 71, 0.3);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-danger" style="font-size: 3rem;"></i>
                <h3 class="fw-bold mt-2">Buat Akun Baru</h3>
                <p class="text-muted">Bergabung dengan komunitas FJB Tel-U</p>
            </div>

            <div class="card card-register bg-white p-4">
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register.process') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">NAMA LENGKAP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border rounded-start-3 text-muted ps-3">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="name" class="form-control border-start-0" placeholder="Nama Lengkap Anda" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">EMAIL ADDRESS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border rounded-start-3 text-muted ps-3">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-start-0" placeholder="nama@email.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">DAFTAR SEBAGAI</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border rounded-start-3 text-muted ps-3">
                                    <i class="bi bi-shop"></i>
                                </span>
                                <select name="role" class="form-select border-start-0" required>
                                    <option value="pembeli">Pembeli (Ingin Belanja)</option>
                                    <option value="penjual">Penjual (Ingin Jualan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border rounded-start-3 text-muted ps-3">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">ULANGI PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border rounded-start-3 text-muted ps-3">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Ketik ulang password" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-telu text-white">
                                Daftar Sekarang
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none">Login disini</a>
                </p>
                <small class="text-muted opacity-50">&copy; 2025 Kelompok 1 WAD</small>
            </div>

        </div>
    </div>
</div>

</body>
</html>