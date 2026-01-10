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
    // 1. Tampilkan Isi Keranjang
    public function index()
    {
        $carts = Cart::where('user_id', Auth::id())->with('product')->get();
        
        $total = 0;
        foreach($carts as $cart) {
            $total += $cart->product->harga * $cart->quantity;
        }

        // Info Libur (Hanya untuk estimasi visual, bukan validasi checkout)
        // Validasi checkout yang ketat ada di TransactionController
        $estimasi = Carbon::now()->addDays(3); 
        $tanggalCek = $estimasi->format('Y-m-d');
        $tahun = $estimasi->format('Y');
        $infoLibur = null;

        try {
            $response = Http::timeout(2)->get("https://api-harilibur.vercel.app/api?year={$tahun}");
            if ($response->successful()) {
                $dataLibur = $response->json();
                foreach ($dataLibur as $libur) {
                    if ($libur['holiday_date'] == $tanggalCek && $libur['is_national_holiday']) {
                        $infoLibur = $libur['holiday_name'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        return view('carts.index', compact('carts', 'total', 'estimasi', 'infoLibur'));
    }

    // 2. Tambah Barang
    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        // Cek stok
        if ($product->stok < 1) {
            return back()->with('error', 'Stok habis!');
        }

        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_id', $productId)
                            ->first();

        if ($existingCart) {
            if ($product->stok < ($existingCart->quantity + 1)) {
                return back()->with('error', 'Stok tidak mencukupi!');
            }
            $existingCart->quantity += 1;
            $existingCart->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
        return redirect()->back()->with('success', 'Produk masuk keranjang!');
    }

    // 3. Hapus Barang
    public function destroy($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cart->delete();
        return back()->with('success', 'Barang dihapus.');
    }

    // 4. PROSES CHECKOUT (MANUAL / TANPA MIDTRANS)
    public function checkout(Request $request)
    {
        $carts = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang masih kosong!');
        }

        return back()->with('error', 'Item tidak ditemukan atau akses ditolak.');
    }
}