<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko Kelompok 1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Toko Kelompok 1</a>
    <div class="d-flex">
        @auth
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-light me-2">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        @endauth
    </div>
  </div>
</nav>

<div class="bg-light py-5">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold">Selamat Datang di Final Project</h1>
        <p class="lead text-muted mb-4">Aplikasi Manajemen Transaksi Sederhana menggunakan Laravel 11.</p>
        
        @auth
            <div class="alert alert-success d-inline-block px-4">
                Halo, <strong>{{ Auth::user()->name }}</strong>! Anda sudah login.
            </div>
            <br><br>
            <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-lg px-5">Lihat Transaksi</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 shadow">Login Sekarang</a>
        @endauth
    </div>
</div>

<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-4">
            <h3>🚀 Cepat</h3>
            <p>Dibuat dengan Laravel 11 terbaru.</p>
        </div>
        <div class="col-md-4">
            <h3>🔒 Aman</h3>
            <p>Dilengkapi sistem Login & Register.</p>
        </div>
        <div class="col-md-4">
            <h3>📱 Responsif</h3>
            <p>Tampilan rapi di HP dan Laptop.</p>
        </div>
    </div>
</div>

</body>
</html>