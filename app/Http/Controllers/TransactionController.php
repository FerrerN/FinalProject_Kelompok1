<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class TransactionController extends Controller
{
    // 1. DAFTAR TRANSAKSI (Logic Pembeli vs Penjual)
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'penjual') {
            // JIKA PENJUAL: Lihat pesanan orang lain yang beli produk saya
            $transactions = Transaction::whereHas('product', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['product', 'user'])->latest()->get();
        } else {
            // JIKA PEMBELI: Lihat riwayat belanja sendiri
            $transactions = Transaction::where('user_id', $user->id)
                ->with('product.user') // Load data toko
                ->latest()->get();
        }

        return view('transactions.index', compact('transactions'));
    }

    // 2. FORM BELI LANGSUNG (Tanpa Keranjang - Optional)
    public function create()
    {
        $products = Product::where('status', 'aktif')->get();
        return view('transactions.create', compact('products'));
    }

    // 3. PROSES SIMPAN TRANSAKSI (BELI LANGSUNG)
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_date' => 'required|date',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cek Stok
        if ($product->stok < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak cukup!']);
        }

        // Buat Transaksi
        Transaction::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity, // Pastikan kolom quantity ada di tabel transactions
            'total_price' => $product->harga * $request->quantity,
            'shipping_date' => $request->shipping_date,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        // Kurangi Stok
        $product->decrement('stok', $request->quantity);

        return redirect()->route('transactions.index')->with('success', 'Pesanan berhasil dibuat!');
    }

    // 4. PROSES UPDATE STATUS (KHUSUS PENJUAL)
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Pastikan yang update adalah pemilik produk (Penjual Asli)
        if ($transaction->product->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah pesanan ini.');
        }

        $request->validate([
            'status' => 'required|in:pending,dikirim,selesai,batal'
        ]);

        $transaction->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // 5. BATALKAN PESANAN (Bisa Pembeli/Penjual)
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Kembalikan Stok jika dibatalkan
        if ($transaction->status != 'batal') {
            $transaction->product->increment('stok', $transaction->quantity ?? 1);
        }

        $transaction->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}