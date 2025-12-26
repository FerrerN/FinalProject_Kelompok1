<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Manajemen Pengguna & Hak Akses</h4>
            <form action="{{ route('admin.users.sync') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm fw-bold">
                    🔄 Sync dari External API
                </button>
            </form>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>External UID</th>
                        <th>Nama & Email</th>
                        <th>Peran (Role)</th>
                        <th>Terakhir Sinkron</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $user->external_uid }}</span></td>
                        <td>
                            <strong>{{ $user->full_name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>
                        <td>
                            <form action="{{ route('admin.users.update-role', $user->local_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="penjual" {{ $user->role == 'penjual' ? 'selected' : '' }}>Penjual</option>
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </form>
                        </td>
                        <td>{{ $user->last_synced->format('d M Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.users.destroy', $user->local_id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Data kosong. Silakan klik tombol Sync.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
