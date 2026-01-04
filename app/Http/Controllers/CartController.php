<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Product;


// --- WAJIB TAMBAHKAN DUA BARIS INI AGAR TIDAK ERROR ---
use Illuminate\Support\Facades\Http; // <--- Untuk API
use Carbon\Carbon;                   // <--- Untuk Tanggal
// -----------------------------------------------------

class CartController extends Controller
{
    // 1. Tampilkan Isi Keranjang
    public function index()
    {
        // 1. Ambil Data Keranjang User
        $carts = Cart::where('user_id', Auth::id())->with('product')->get();
        
        // 2. Hitung Total Bayar
        $total = 0;
        foreach($carts as $cart) {
            $total += $cart->product->harga * $cart->quantity;
        }

        // --- INTEGRASI API HARI LIBUR (UNTUK TAMPILAN) ---
        
        // A. Tentukan Estimasi (Misal: 3 Hari dari Sekarang)
        $estimasi = Carbon::create(2026, 01, 7); // Simulasi Tanggal Merah
        $tanggalCek = $estimasi->format('Y-m-d');
        $tahun = $estimasi->format('Y');

        $infoLibur = null; // Default kosong

        try {
            // B. Tembak API Hari Libur
            $response = Http::timeout(2)->get("https://api-harilibur.vercel.app/api?year={$tahun}");
            
            if ($response->successful()) {
                $dataLibur = $response->json();
                
                // C. Cek apakah estimasi tanggal kita == hari libur
                foreach ($dataLibur as $libur) {
                    if ($libur['holiday_date'] == $tanggalCek && $libur['is_national_holiday']) {
                        $infoLibur = $libur['holiday_name']; // Simpan nama hari liburnya
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika API error/lemot, biar web tetap jalan
        }

        // Kirim data ke View (carts, total, estimasi, infoLibur)
        return view('carts.index', compact('carts', 'total', 'estimasi', 'infoLibur'));
    }

    // 2. Tambah Barang ke Keranjang
    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Cek apakah barang sudah ada di keranjang user?
        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_id', $productId)
                            ->first();

        if ($existingCart) {
            // Jika sudah ada, tambah jumlahnya saja
            $existingCart->quantity += 1;
            $existingCart->save();
        } else {
            // Jika belum ada, buat baru
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }

        return redirect()->back()->with('success', 'Produk masuk keranjang!');
    }

    // 3. Hapus Barang dari Keranjang
    public function destroy($id)
    {
        // Cari item keranjang berdasarkan ID dan pastikan punya user yang sedang login
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        
        $cart->delete();

        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    // 4. Proses Checkout (Semua barang di keranjang jadi Transaksi)
    public function checkout(Request $request)
    {
        $carts = Cart::where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang masih kosong!');
        }

        foreach ($carts as $cart) {
            // Simpan ke tabel Transaksi
            Transaction::create([
                'user_id' => Auth::id(),
                'product_id' => $cart->product_id,
                'shipping_date' => now()->addDays(2), // Default kirim lusa
                'total_price' => $cart->product->harga * $cart->quantity,
                'status' => 'pending',
                'notes' => 'Checkout via Keranjang'
            ]);
            
            // Kurangi Stok Produk (Opsional, tapi bagus ada)
            $cart->product->decrement('stok', $cart->quantity);
        }

        // Kosongkan Keranjang setelah checkout
        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('transactions.index')->with('success', 'Checkout berhasil! Pesanan sedang diproses.');
    }
}