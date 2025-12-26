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
        // Data Dummy disesuaikan
        $externalData = [
            ['uid' => 'EXT-101', 'email' => 'admin@toko.com', 'name' => 'Budi Admin'],
            ['uid' => 'EXT-102', 'email' => 'siti@seller.com', 'name' => 'Siti Penjual'], 
            ['uid' => 'EXT-103', 'email' => 'joko@pembeli.com', 'name' => 'Joko User'],
        ];

        foreach ($externalData as $data) {
            UserMapping::updateOrCreate(
                ['external_uid' => $data['uid']],
                [
                    'email' => $data['email'],
                    'full_name' => $data['name'],
                    'last_synced' => now()
                ]
            );
        }

        return redirect()->back()->with('success', 'Sinkronisasi berhasil!');
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
