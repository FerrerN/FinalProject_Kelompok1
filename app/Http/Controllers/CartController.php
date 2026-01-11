<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http; 
use Carbon\Carbon;

class CartController extends Controller
{
    // 1. TAMPILKAN ISI KERANJANG
    public function index()
    {
        // Ambil data keranjang milik user yang sedang login
        $carts = Cart::where('user_id', Auth::id())
                    ->with('product') // Eager loading produk biar ringan
                    ->get();
        
        // Hitung Total Harga
        $total = 0;
        foreach($carts as $cart) {
            // Pastikan produk masih ada (mencegah error jika produk dihapus admin)
            if($cart->product) {
                $total += $cart->product->harga * $cart->quantity;
            }
        }

        // ==========================================
        // FITUR API HARI LIBUR (Estimasi Pengiriman)
        // ==========================================
        // Estimasi pengiriman = Hari ini + 3 hari
        $estimasi = Carbon::now()->addDays(3); 
        $tanggalCek = $estimasi->format('Y-m-d');
        $tahun = $estimasi->format('Y');
        $infoLibur = null;

        try {
            // Timeout 2 detik agar web tidak loading lama jika API down
            $response = Http::timeout(2)->get("https://api-harilibur.vercel.app/api?year={$tahun}");
            
            if ($response->successful()) {
                $dataLibur = $response->json();
                foreach ($dataLibur as $libur) {
                    // Cek apakah tanggal estimasi jatuh di hari libur nasional
                    if ($libur['holiday_date'] == $tanggalCek && $libur['is_national_holiday']) {
                        $infoLibur = $libur['holiday_name'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika API error/timeout, abaikan saja (Silent fail) agar web tetap jalan
            $infoLibur = null; 
        }

        // Kirim data ke View
        return view('carts.index', compact('carts', 'total', 'estimasi', 'infoLibur'));
    }

    // ==========================================
    // 2. TAMBAH BARANG KE KERANJANG
    // ==========================================
    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        // 1. Cek stok utama di database produk
        if ($product->stok < 1) {
            return back()->with('error', 'Stok habis!');
        }

        // 2. Cek apakah barang sudah ada di keranjang user?
        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_id', $productId)
                            ->first();

        if ($existingCart) {
            // Validasi: Jangan biarkan user menambah melebihi stok yang ada
            if ($product->stok < ($existingCart->quantity + 1)) {
                return back()->with('error', 'Stok tidak mencukupi untuk menambah jumlah!');
            }

            // Jika aman, tambah quantity
            $existingCart->quantity += 1;
            $existingCart->save();
        } else {
            // Jika belum ada, buat baris baru di keranjang
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
        
        return redirect()->back()->with('success', 'Produk berhasil masuk keranjang!');
    }

    // ==========================================
    // 3. HAPUS BARANG DARI KERANJANG
    // ==========================================
    public function destroy($id)
    {
        // Cari cart berdasarkan ID dan pastikan milik user yang login (Security)
        $cart = Cart::where('user_id', Auth::id())
                    ->where('id', $id)
                    ->firstOrFail();

        $cart->delete();

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }
    
}