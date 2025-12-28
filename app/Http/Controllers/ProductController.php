<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File; // Tambahan untuk hapus file lama

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

    // 3. SIMPAN PRODUK (DENGAN UPLOAD GAMBAR)
    // Jangan lupa import Facade File di paling atas file controller
  

    public function store(Request $request)
    {
        // Cek error asli dari PHP
        if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        if (!$file->isValid()) {
            dd($file->getErrorMessage()); // Ini akan memunculkan pesan error di layar putih
        }
        }
        // 1. Validasi
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'deskripsi'   => 'required|string',
            'gambar'      => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        // 2. Siapkan Folder Upload
        $path = public_path('uploads/products');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true); // Buat folder jika belum ada
        }

        // 3. Proses Upload Gambar
        $url_gambar = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(); // Nama unik
            
            $file->move($path, $filename);
            
            $url_gambar = asset('uploads/products/' . $filename);
        }

        // 4. Simpan ke Database
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

        // 5. Redirect ke Halaman List Produk Saya
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK: Anda bukan pemilik produk ini!');
        }

        return view('products.edit', compact('product'));
    }

    // 5. UPDATE PRODUK (DENGAN UPLOAD GAMBAR)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK: Jangan mencoba mengedit produk orang lain!');
        }

        $request->validate([
            'nama_barang' => 'required',
            'kategori'    => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|integer',
            'deskripsi'   => 'required',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Nullable: boleh tidak upload baru
        ]);

        $dataUpdate = [
            'nama_barang' => $request->nama_barang,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'deskripsi'   => $request->deskripsi,
        ];

        // Cek jika ada upload gambar baru
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Simpan file baru
            $file->move(public_path('uploads/products'), $filename);
            
            // Hapus file lama jika ada (opsional, biar server gak penuh)
            $oldPath = str_replace(url('/'), public_path(), $product->url_gambar);
            if (File::exists($oldPath)) {
                // File::delete($oldPath); // Uncomment baris ini jika ingin hapus file lama otomatis
            }

            // Update URL di database
            $dataUpdate['url_gambar'] = asset('uploads/products/' . $filename);
        }

        $product->update($dataUpdate);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    // 6. HAPUS PRODUK
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk dihapus!');
    }

    // 7. DETAIL PRODUK
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        $reviewableTransaction = null;
        if (Auth::check()) {
            $reviewableTransaction = \App\Models\Transaction::where('user_id', Auth::id())
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
        if (Auth::user()->role !== 'penjual') {
            abort(403);
        }
        $products = Product::where('user_id', Auth::id())->get();
        $pdf = Pdf::loadView('products.stock_report_pdf', compact('products'));
        return $pdf->download('laporan-stok-'.date('Y-m-d').'.pdf');
    }
    
}