<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; }
        form { width: 300px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <form action="{{ route('register.submit') }}" method="POST">
        @csrf
        <h2 style="text-align:center">Daftar</h2>

        <div>
            <label>Nama Lengkap</label>
            <input type="text" name="name" required>
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <label>Daftar Sebagai:</label>
            <select name="role">
                <option value="buyer">Pembeli</option>
                <option value="seller">Penjual</option>
                </select>
        </div>

        <button type="submit">Daftar Sekarang</button>
        <p style="text-align:center; font-size:12px;">
            Sudah punya akun? <a href="{{ route('login') }}">Login</a>
        </p>
    </form>
</body>
</html>
