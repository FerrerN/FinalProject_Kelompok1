<?php

namespace App\Http\Controllers;

use App\Models\UserMapping;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Menampilkan halaman list user
    public function index()
    {
        $users = UserMapping::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    // Simulasi tombol "Sync" untuk menarik data dari API External
    public function sync()
    {
        // CERITANYA: Ini data yang didapat dari API External (Firebase/Auth0)
        // Nanti diganti dengan request API beneran
        $dummyExternalData = [
            ['uid' => 'EXT-881', 'email' => 'admin.utama@example.com', 'name' => 'Budi Santoso'],
            ['uid' => 'EXT-882', 'email' => 'staff.gudang@example.com', 'name' => 'Siti Aminah'],
            ['uid' => 'EXT-883', 'email' => 'member.baru@example.com', 'name' => 'Joko Widodo'],
        ];

        foreach ($dummyExternalData as $data) {
            UserMapping::updateOrCreate(
                ['external_uid' => $data['uid']], // Kunci pencarian
                [
                    'email' => $data['email'],
                    'full_name' => $data['name'],
                    'last_synced' => now()
                ]
            );
        }

        return redirect()->back()->with('success', 'Sinkronisasi Data Berhasil!');
    }

    // Update Role User
    public function updateRole(Request $request, $id)
    {
        $user = UserMapping::findOrFail($id);
        $user->update(['role' => $request->role]);
        return redirect()->back()->with('success', 'Hak akses berhasil diubah.');
    }

    // Hapus User
    public function destroy($id)
    {
        $user = UserMapping::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
