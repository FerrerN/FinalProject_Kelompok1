<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction; // Jangan lupa import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    // 1. Tampilkan Isi Keranjang
    public function index()
    {
        $carts = Cart::where('user_id', Auth::id())->with('product')->get();
        
        // Hitung Total Harga
        $total = $carts->sum(function($cart) {
            return $cart->product->harga * $cart->quantity;
        });

        return view('carts.index', compact('carts', 'total'));
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
        Cart::destroy($id);
        return redirect()->back()->with('success', 'Barang dihapus dari keranjang.');
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