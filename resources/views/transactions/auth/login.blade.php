<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; }
        form { width: 300px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .error { color: red; font-size: 12px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <h2 style="text-align:center">Login</h2>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Masuk</button>
        <p style="text-align:center; font-size:12px;">
            Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
        </p>
    </form>
</body>
</html>
