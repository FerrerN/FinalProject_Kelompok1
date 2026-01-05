<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    // 1. Tampilkan Form Edit Profil
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    // 2. Update Data Profil
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        // Validasi input
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|numeric',
            'address' => 'nullable|string',
            'bio'     => 'nullable|string',
        ]);

        // Update data di database
        $user->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'bio'     => $request->bio,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}