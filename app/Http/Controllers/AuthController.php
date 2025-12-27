<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    // 1. TAMPILKAN FORM LOGIN (Ini yang tadi Error/Hilang)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. PROSES LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect ke Home setelah login sukses
            return redirect()->route('home')->with('success', 'Login berhasil! Selamat datang.');
        }

        // Jika gagal, kembali ke login dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // 3. TAMPILKAN FORM REGISTER
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // 4. PROSES REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:pembeli,penjual' // Validasi Role
        ]);

        // Buat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Langsung Login setelah daftar
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat! Selamat datang di FJB Tel-U.');
    }

    // 5. PROSES LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}