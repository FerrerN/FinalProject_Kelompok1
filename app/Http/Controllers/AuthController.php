<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller; // <--- PERBAIKAN: Tambahkan baris ini!

class AuthController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek ke Database (Otomatis hash matching)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect ke halaman transaksi setelah login sukses
            return redirect()->intended('transactions')->with('success', 'Berhasil Login!');
        }

        // Jika salah
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Redirect ke halaman awal (Landing Page)
        return redirect('/');
    }
}