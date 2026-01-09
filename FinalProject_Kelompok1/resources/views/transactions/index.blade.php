<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Daftar Transaksi Kelompok 1</h2>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary shadow-sm">
            + Transaksi Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pembeli</th>
                        <th>Produk</th>
                        <th>Tanggal Kirim</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="fw-bold">#{{ $trx->id }}</td>
                        <td>{{ $trx->user->name ?? 'Guest' }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $trx->product->nama_barang ?? 'Produk Dihapus' }}</span>
                                <small class="text-muted">Qty: {{ number_format($trx->total_price / ($trx->product->harga ?? 1)) }}</small>
                            </div>
                        </td>
                        <td>{{ date('d M Y', strtotime($trx->shipping_date)) }}</td>
                        <td class="text-success fw-bold">Rp {{ number_format($trx->total_price) }}</td>
                        <td>
                            @if($trx->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($trx->status == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-secondary">{{ $trx->status }}</span>
                            @endif
                        </td>
                        <td>
                            <form onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');" action="{{ route('transactions.destroy', $trx->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Batal
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="60" class="mb-2 opacity-50"><br>
                            Belum ada transaksi saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>