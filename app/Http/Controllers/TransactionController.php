<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller; // <--- TAMBAHKAN BARIS INI (SOLUSI ERROR BARIS 9)

class TransactionController extends Controller
{
    // Halaman Daftar Transaksi
    public function index()
    {
        // Menggunakan with() untuk Eager Loading agar query lebih ringan
        $transactions = Transaction::with(['user', 'product'])->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    // Halaman Form Checkout
    public function create()
    {
        // Pastikan baris ini ada!
        $products = Product::where('status', 'aktif')->get(); 
        
        // Pastikan compact('products') ada!
        return view('transactions.create', compact('products'));
    }

    // Proses Simpan Transaksi
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'shipping_date' => 'required|date',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $total_price = $product->harga * $request->quantity;

        // Simpan ke database
        Transaction::create([
            'user_id' => 1, // Sementara hardcode ID user 1
            'product_id' => $product->id,
            'shipping_date' => $request->shipping_date,
            'total_price' => $total_price,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return redirect()->route('transactions.index')->with('success', 'Pesanan berhasil dibuat!');
    }

    // Fitur Batal Pesanan (Delete)
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete(); // Soft Delete

        return redirect()->route('transactions.index')->with('success', 'Pesanan dibatalkan.');
    }
}