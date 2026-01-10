<!DOCTYPE html>
<html>
<head>
    <title>Laporan Sistem Admin</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Ringkasan FJB Tel-U</h2>
        <p>Tanggal: {{ $date }}</p>
    </div>

    <h3>Daftar Pengguna</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Daftar Produk</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Penjual</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->nama_barang }}</td>
                <td>{{ $product->user->name }}</td>
                <td>Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                <td>{{ $product->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
