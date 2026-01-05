<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http; // Untuk API Libur (Tetap dipakai)
use Carbon\Carbon;                   // Untuk Tanggal


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

        // --- API HARI LIBUR GRATIS (TETAP ADA) ---
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
        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_id', $productId)
                            ->first();

        if ($existingCart) {
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

        // Ambil Tanggal dari Input view atau Default H+3
        $shippingDate = $request->input('shipping_date', now()->addDays(3));

        foreach ($carts as $cart) {
            
            // Cek Stok
            if ($cart->product->stok < $cart->quantity) {
                return back()->withErrors(['error' => 'Stok ' . $cart->product->nama_barang . ' habis!']);
            }

            // Simpan Transaksi
            Transaction::create([
                'user_id'       => Auth::id(),
                'product_id'    => $cart->product_id,
                'quantity'      => $cart->quantity,
                'shipping_date' => $shippingDate,
                'total_price'   => $cart->product->harga * $cart->quantity,
                'status'        => 'pending', // Status awal pending, nanti diupdate penjual
                'notes'         => 'Menunggu Pembayaran Manual'
            ]);
            
            // (KODE MIDTRANS SUDAH DIHAPUS DISINI)

            // Kurangi Stok
            $cart->product->decrement('stok', $cart->quantity);
        }

        // Kosongkan Keranjang
        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('transactions.index')->with('success', 'Checkout berhasil! Silakan hubungi penjual untuk pembayaran.');
    }
}