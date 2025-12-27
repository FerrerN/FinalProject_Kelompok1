<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-telu { background: linear-gradient(135deg, #b91d47 0%, #ee395f 100%); }
        .card-trx { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .status-badge { font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; font-weight: 600; }
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; transition: 0.2s; }
        .btn-icon:hover { background-color: #e9ecef; transform: scale(1.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}"><i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U</a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small d-none d-md-block">Halo, {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Kembali</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">
                    @if(Auth::user()->role == 'penjual')
                        Manajemen Pesanan Masuk
                    @else
                        Riwayat Belanja Saya
                    @endif
                </h3>
                <p class="text-muted m-0">Pantau status transaksi Anda di sini.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-trx bg-white">
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">No</th>
                                    <th>Detail Produk</th>
                                    <th>
                                        @if(Auth::user()->role == 'penjual') Pembeli @else Penjual @endif
                                    </th>
                                    <th>Total & Tgl</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index => $trx)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $trx->product->url_gambar ?? 'https://via.placeholder.com/50' }}" 
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <span class="fw-bold d-block text-dark">{{ $trx->product->nama_barang }}</span>
                                                    <small class="text-muted">Qty: {{ $trx->quantity ?? 1 }} pcs</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if(Auth::user()->role == 'penjual')
                                                <i class="bi bi-person-circle text-secondary me-1"></i> {{ $trx->user->name }}
                                            @else
                                                <i class="bi bi-shop text-danger me-1"></i> {{ $trx->product->user->name ?? 'Toko' }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-danger">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($trx->shipping_date)->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'pending' => 'bg-warning text-dark bg-opacity-25',
                                                    'dikirim' => 'bg-info text-dark bg-opacity-25',
                                                    'selesai' => 'bg-success text-success bg-opacity-10',
                                                    'batal'   => 'bg-danger text-danger bg-opacity-10',
                                                ];
                                                $class = $badges[$trx->status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="status-badge {{ $class }} text-uppercase">
                                                {{ $trx->status }}
                                            </span>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                
                                                <a href="{{ route('transactions.print', $trx->id) }}" target="_blank" class="btn-icon text-dark" title="Cetak Invoice">
                                                    <i class="bi bi-printer"></i>
                                                </a>

                                                @if(Auth::user()->role == 'penjual')
                                                    <form action="{{ route('transactions.update', $trx->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('PUT')
                                                        <select name="status" class="form-select form-select-sm border-secondary" style="width: 100px;" onchange="this.form.submit()">
                                                            <option value="pending" {{ $trx->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="dikirim" {{ $trx->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                            <option value="selesai" {{ $trx->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                            <option value="batal"   {{ $trx->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                                        </select>
                                                    </form>
                                                @endif

                                                @if($trx->status == 'pending' || $trx->status == 'batal')
                                                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Yakin hapus transaksi ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn-icon text-danger border-0 bg-transparent" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted opacity-25" style="font-size: 4rem;"></i>
                        <p class="mt-3 text-muted">Belum ada data transaksi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>