<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    // --- 1. DASHBOARD ---
    public function dashboard()
    {
        // 1. Ambil Data Statistik
        $stats = [
            'total_users' => User::count(),
            'active_sessions' => DB::table('sessions')->count(),
            'total_products' => Product::count(),
            'recent_users' => User::latest()->take(5)->get()
        ];

        // 2. Ambil Data Cuaca
        // Hapus Cache dulu biar update: php artisan cache:clear
        $weather = Cache::remember('weather_data', 3600, function () {
            
            // Ambil dari config yang baru kita buat
            $apiKey = config('services.openweather.key');
            $city = config('services.openweather.city');

            // Cek apakah API Key ada?
            if (!$apiKey) {
                return null; // Kalau gak ada key, langsung N/A
            }

            try {
                // Tambahkan withoutVerifying() untuk bypass SSL Error di Localhost
                $response = Http::withoutVerifying()->timeout(5)->get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'lang' => 'id'
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
                
                return null;
            } catch (\Exception $e) {
                // Uncomment baris bawah ini jika ingin lihat pesan errornya di layar
                // dd($e->getMessage());
                return null;
            }
        });

        return view('admin.dashboard', compact('stats', 'weather'));
    }

    // --- 2. USER MANAGEMENT ---

    public function users(Request $request)
    {
        $query = User::latest();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,penjual,pembeli',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', 'Akun pengguna berhasil dihapus beserta seluruh datanya.');
    }

    // --- 3. PRODUCT MANAGEMENT ---

    public function products()
    {
        $products = Product::with('user')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        return view('admin.products.create');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'kategori' => 'required|string',
            'deskripsi' => 'nullable|string',
            'url_gambar' => 'required|url',
        ]);

        Product::create([
            'user_id' => auth()->id(),
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'url_gambar' => $request->url_gambar,
            'status' => 'aktif'
        ]);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'status' => 'required|in:aktif,terjual,ditarik',
            'url_gambar' => 'required|url',
        ]);

        $product->update($request->all());

        return redirect()->route('admin.products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus permanen.');
    }
}
