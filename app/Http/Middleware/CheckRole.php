<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next, string $role): Response
{
        // Cek 1: Apakah sudah login?
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek 2: Apakah role user sesuai dengan yang diminta?
        if (Auth::user()->role !== $role) {
            // Jika Admin, boleh akses semuanya (Opsional, fitur "Super Admin")
            // if (Auth::user()->role == 'admin') return $next($request);

            abort(403, 'Anda tidak memiliki akses ke halaman ini!');
        }

        return $next($request);
    }
}
