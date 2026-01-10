<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. DAFTAR SEMUA PRODUK
    public function index()
    {
        $products = Product::where('status', 'aktif')->latest()->get();
        return view('products.index', compact('products'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        return view('products.create');
    }

    // 3. SIMPAN PRODUK
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'deskripsi'   => 'required|string',
            'gambar'      => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $url_gambar = null;
        
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('marketplace_products', 'cloudinary');
            $url_gambar = Storage::disk('cloudinary')->url($path);
        }

        Product::create([
            'user_id'     => Auth::id(),
            'nama_barang' => $request->nama_barang,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'deskripsi'   => $request->deskripsi,
            'url_gambar'  => $url_gambar,
            'status'      => 'aktif',
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupload via Storage!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        if ($product->user_id !== Auth::id()) abort(403);
        return view('products.edit', compact('product'));
    }

    // 5. UPDATE PRODUK
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->user_id !== Auth::id()) abort(403);

        $request->validate([
            'nama_barang' => 'required',
            'kategori'    => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|integer',
            'deskripsi'   => 'required',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $dataUpdate = [
            'nama_barang' => $request->nama_barang,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'deskripsi'   => $request->deskripsi,
        ];

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('marketplace_products', 'cloudinary');
            $dataUpdate['url_gambar'] = Storage::disk('cloudinary')->url($path);
        }

        $product->update($dataUpdate);
        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    // 6. HAPUS PRODUK (SUDAH DI-MERGE DENGAN FITUR HAPUS GAMBAR)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Cek Pemilik
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        // --- TAMBAHAN: HAPUS GAMBAR DARI CLOUDINARY ---
        // Kita cek dulu, apakah produk ini punya gambar?
        if ($product->url_gambar) {
            try {
                // 1. Ambil nama file saja dari URL panjang itu
                // Contoh URL: https://res.cloudinary.../marketplace_products/ab123.jpg
                // Hasil basename: ab123.jpg
                $filename = basename($product->url_gambar);

                // 2. Tentukan lokasinya (Folder + Nama File)
                // Harus sama persis dengan nama folder waktu kita upload di method store()
                $path = 'marketplace_products/' . $filename;

                // 3. Perintahkan Storage untuk menghapus
                Storage::disk('cloudinary')->delete($path);

            } catch (\Exception $e) {
                // Kalau gagal hapus gambar (misal sudah hilang duluan), biarkan saja.
                // Jangan sampai error gambar menghalangi penghapusan produk.
            }
        }
        // ------------------------------------------------

        // Hapus data di database
        $product->delete();
        
        return redirect()->route('products.index')->with('success', 'Produk dan gambarnya berhasil dihapus!');
    }

    // 7. DETAIL PRODUK
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $reviewableTransaction = null;
        if (Auth::check()) {
            $reviewableTransaction = Transaction::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->where('status', 'selesai')
                ->whereDoesntHave('review')
                ->latest()
                ->first();
        }
        return view('products.show', compact('product', 'reviewableTransaction'));
    }

    // 8. EXPORT PDF
    public function exportStockReport()
    {
        // Validasi Role
        if (Auth::user()->role !== 'penjual') {
            abort(403, 'Unauthorized action.');
        }

        $products = Product::where('user_id', Auth::id())->get();

        // Load view PDF
        $pdf = Pdf::loadView('products.stock_report_pdf', compact('products'));

        // Set ukuran kertas
        $pdf->setPaper('a4', 'portrait');

        // Return download
        return $pdf->download('Laporan_Stok_'. date('Y-m-d_H-i') .'.pdf');
    }
}