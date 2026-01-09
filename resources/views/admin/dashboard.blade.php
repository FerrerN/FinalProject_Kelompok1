<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #b91d47 0%, #ee395f 100%);
            color: white;
        }
        .nav-link { color: rgba(255,255,255,0.8); margin-bottom: 5px; border-radius: 5px; padding: 10px 15px; }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
            font-weight: bold;
        }
        .card-stat { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: .3s; }
        .card-stat:hover { transform: translateY(-5px); }
        .stat-value { font-size: 2.5rem; font-weight: bold; color: #333; }
        .stat-label { color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .top-bar { background-color: white; border-bottom: 1px solid #eee; padding: 15px 30px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-2 sidebar p-4 d-none d-md-block fixed-top" style="bottom:0; z-index:100;">
            <h4 class="fw-bold mb-4 d-flex align-items-center">
                <i class="bi bi-shield-lock-fill me-2"></i> FJB Admin
            </h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.users') }}">
                        <i class="bi bi-people me-2"></i> User Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.products') }}">
                        <i class="bi bi-box-seam me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item mt-5">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link bg-transparent border-0 text-start w-100 text-white">
                            <i class="bi bi-box-arrow-left me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="col-md-10 offset-md-2 p-0">
            <div class="top-bar d-flex justify-content-between align-items-center sticky-top shadow-sm">
                <div>
                    <h5 class="m-0 fw-bold">Dashboard Overview</h5>
                    <small class="text-muted">Welcome back, {{ Auth::user()->name }}</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success rounded-pill px-3">System Online</span>
                </div>
            </div>

            <div class="p-4 container-fluid">

                <div class="row g-4 mb-5">

                    <div class="col-md-3">
                        <div class="card card-stat bg-white p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label mb-1">Total Users</div>
                                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                    <i class="bi bi-people-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card card-stat bg-white p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label mb-1">Active Sessions</div>
                                    <div class="stat-value">{{ $stats['active_sessions'] }}</div>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                    <i class="bi bi-activity fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card card-stat bg-white p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label mb-1">Total Products</div>
                                    <div class="stat-value">{{ $stats['total_products'] }}</div>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                    <i class="bi bi-box-seam-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card card-stat text-white p-3 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if(isset($weather) && isset($weather['main']))
                                        <div class="stat-label text-white-50 mb-1">Cuaca {{ $weather['name'] }}</div>
                                        <div class="stat-value text-white" style="font-size: 2rem;">{{ round($weather['main']['temp']) }}°C</div>
                                        <div class="small text-white-50 text-capitalize">
                                            {{ $weather['weather'][0]['description'] }}
                                        </div>
                                    @else
                                        <div class="stat-label text-white-50">Cuaca</div>
                                        <div class="stat-value text-white">-</div>
                                        <div class="small">Data N/A</div>
                                    @endif
                                </div>
                                @if(isset($weather) && isset($weather['weather']))
                                    <img src="https://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@2x.png" alt="Icon" width="60">
                                @else
                                    <i class="bi bi-cloud-slash fs-1 text-white-50"></i>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2"></i>Pengguna Terbaru</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4">Nama</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Bergabung</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stats['recent_users'] as $user)
                                            <tr>
                                                <td class="ps-4 fw-bold">{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'penjual' ? 'bg-info' : 'bg-secondary') }}">
                                                        {{ ucfirst($user->role) }}
                                                    </span>
                                                </td>
                                                <td class="text-muted small">{{ $user->created_at->diffForHumans() }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="fw-bold m-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-3">
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary py-3 d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-people-fill me-2"></i> Manage Users</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                    <a href="{{ route('admin.products') }}" class="btn btn-outline-danger py-3 d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-box-seam me-2"></i> Manage Products</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>

                                    {{-- TOMBOL EXPORT PDF YANG SUDAH DIAKTIFKAN --}}
                                    <a href="{{ route('admin.export_report') }}" class="btn btn-outline-success py-3 d-flex justify-content-between align-items-center text-decoration-none">
                                        <span><i class="bi bi-file-earmark-pdf-fill me-2"></i> Export Report (PDF)</span>
                                        <i class="bi bi-download"></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="mt-5 text-muted small text-center">
                    &copy; 2025 Admin System FJB Tel-U. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
