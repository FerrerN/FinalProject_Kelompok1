<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Tambahkan ini di paling atas
use Illuminate\Routing\Controller;


class ProductController extends Controller
{
    /**
     * 1. MENAMPILKAN DAFTAR PRODUK (INDEX)
     * Digunakan di halaman /products
     */
    public function index()
    {
        // Ambil data asli dari database (bukan dummy lagi)
        // 'latest()' agar produk yang baru ditambah muncul paling atas
        $products = Product::where('status', 'aktif')->latest()->get();

        // Kirim data ke view 'products.index'
        return view('products.index', compact('products'));
    }

    /**
     * 2. FORM TAMBAH PRODUK
     * Hanya bisa diakses oleh Penjual
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * 3. PROSES SIMPAN PRODUK BARU
     */
    public function store(Request $request)
{
    $request->validate([
        'nama_barang' => 'required|string|max:255',
        'harga'       => 'required|numeric|min:0',
        'stok'        => 'required|integer|min:0',
        'deskripsi'   => 'required|string',
        'url_gambar'  => 'required|url',
    ]);

    Product::create([
        'user_id'     => Auth::id(), // <--- OTOMATIS AMBIL ID YANG LOGIN
        'nama_barang' => $request->nama_barang,
        'harga'       => $request->harga,
        'stok'        => $request->stok,
        'deskripsi'   => $request->deskripsi,
        'url_gambar'  => $request->url_gambar,
        'status'      => 'aktif',
    ]);

    return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
}

    /**
     * 4. FORM EDIT PRODUK
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * 5. PROSES UPDATE PRODUK
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga'       => 'required|numeric',
            'stok'        => 'required|integer',
            'url_gambar'  => 'required|url',
        ]);

        $product = Product::findOrFail($id);
        
        $product->update([
            'nama_barang' => $request->nama_barang,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'deskripsi'   => $request->deskripsi,
            'url_gambar'  => $request->url_gambar,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * 6. HAPUS PRODUK
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * 7. DETAIL PRODUK (Opsional)
     * Jika ingin melihat detail satu produk
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }
}