<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth; // <--- 1. PENTING: TAMBAHKAN BARIS INI

class TransactionController extends Controller
{
    // Halaman Daftar Transaksi
    public function index()
    {
        $transactions = Transaction::with(['user', 'product'])->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    // Halaman Form Checkout
    public function create()
    {
        $products = Product::where('status', 'aktif')->get();
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
            'user_id' => Auth::id(), // <--- 2. UBAH JADI INI (Lebih aman & tidak merah)
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