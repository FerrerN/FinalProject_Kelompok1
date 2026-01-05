<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management - Admin FJB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #b91d47 0%, #ee395f 100%); color: white; }
        .nav-link { color: rgba(255,255,255,0.8); margin-bottom: 5px; border-radius: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background-color: rgba(255,255,255,0.2); font-weight: bold; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 sidebar p-4 d-none d-md-block fixed-top" style="bottom:0; z-index:100;">
            <h4 class="fw-bold mb-4"><i class="bi bi-shield-lock-fill me-2"></i> FJB Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.users') }}"><i class="bi bi-people me-2"></i> User Management</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.products') }}"><i class="bi bi-box-seam me-2"></i> Products</a></li>
                <li class="nav-item mt-5">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link bg-transparent border-0 text-start w-100 text-white"><i class="bi bi-box-arrow-left me-2"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="col-md-10 offset-md-2 p-0">
            <div class="bg-white border-bottom p-3 sticky-top">
                <h5 class="m-0 fw-bold">User Management</h5>
            </div>

            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <ul class="nav nav-pills card-header-pills">
                            <li class="nav-item">
                                <a class="nav-link {{ request('role') == '' ? 'active bg-danger' : 'text-secondary' }}" href="{{ route('admin.users') }}">Semua</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('role') == 'pembeli' ? 'active bg-danger' : 'text-secondary' }}" href="{{ route('admin.users', ['role' => 'pembeli']) }}">Pembeli</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('role') == 'penjual' ? 'active bg-danger' : 'text-secondary' }}" href="{{ route('admin.users', ['role' => 'penjual']) }}">Penjual</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('role') == 'admin' ? 'active bg-danger' : 'text-secondary' }}" href="{{ route('admin.users', ['role' => 'admin']) }}">Admin</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-secondary text-uppercase small">
                                    <tr>
                                        <th class="ps-4">Nama User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Terdaftar</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->role == 'pembeli') <span class="badge bg-secondary">Pembeli</span>
                                            @elseif($user->role == 'penjual') <span class="badge bg-info text-dark">Penjual</span>
                                            @else <span class="badge bg-danger">Admin</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>

                                                @if($user->id != Auth::id())
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus User {{ $user->name }}? \nPERINGATAN: Semua produk dan riwayat transaksi user ini juga akan terhapus!');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-light text-muted" disabled><i class="bi bi-trash"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Tidak ada data user ditemukan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 py-3">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
