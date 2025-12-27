<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-telu {
            background: linear-gradient(to right, #b91d47, #ee395f);
        }
        .card-table {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .table thead th {
            background-color: #f1f5f9;
            color: #495057;
            font-weight: 600;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .btn-action:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-white small d-none d-md-block">Halo, {{ Auth::user()->name }}</span>
                <a href="{{ route('home') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Home
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Riwayat Transaksi</h3>
                <p class="text-muted m-0">Pantau status pesanan barang Anda di sini.</p>
            </div>
            </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-table bg-white">
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover">
                            <thead>
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Produk</th>
                                    <th>Tanggal Pengiriman</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index => $transaction)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3 text-danger">
                                                    <i class="bi bi-box-seam" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">{{ $transaction->product->nama_barang ?? 'Produk Terhapus' }}</span>
                                                    <small class="text-muted">ID: #TRX-{{ $transaction->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar3 me-2 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($transaction->shipping_date)->format('d M Y') }}
                                        </td>
                                        <td class="fw-bold text-danger">
                                            Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($transaction->status) {
                                                    'pending' => 'bg-warning text-dark bg-opacity-25',
                                                    'dikirim' => 'bg-info text-dark bg-opacity-25',
                                                    'selesai' => 'bg-success text-success bg-opacity-10',
                                                    'batal'   => 'bg-danger text-danger bg-opacity-10',
                                                    default   => 'bg-secondary text-secondary bg-opacity-10',
                                                };
                                                
                                                // Ubah teks status jadi Title Case (Pending, Selesai)
                                                $statusText = ucfirst($transaction->status);
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn-action btn-light text-primary" title="Edit / Detail">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transaksi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-light text-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-cart-x text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                        </div>
                        <h5 class="text-muted fw-bold">Belum Ada Transaksi</h5>
                        <p class="text-muted small mb-4">Anda belum pernah melakukan pembelian barang apapun.</p>
                        <a href="{{ route('home') }}" class="btn btn-danger rounded-pill px-4 fw-bold">
                            Mulai Belanja
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-4 text-muted small">
            &copy; 2025 FJB Tel-U - Web Application Development
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>