<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class ReviewController extends Controller
{
    // 1. FORM TULIS ULASAN (CREATE)
    public function create($transactionId)
    {
        // Cek apakah transaksi milik user dan statusnya selesai
        $transaction = Transaction::where('id', $transactionId)
                        ->where('user_id', Auth::id())
                        ->where('status', 'selesai')
                        ->firstOrFail();

        // Cek apakah sudah pernah diulas?
        $existingReview = Review::where('transaction_id', $transactionId)->first();
        if ($existingReview) {
            return redirect()->route('products.show', $transaction->product_id)
                    ->with('error', 'Anda sudah mengulas transaksi ini.');
        }

        return view('reviews.create', compact('transaction'));
    }

    // 2. SIMPAN ULASAN (STORE)
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'required|string'
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'transaction_id' => $request->transaction_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('products.show', $request->product_id)
                ->with('success', 'Ulasan berhasil dikirim!');
    }

    // 3. FORM EDIT ULASAN (EDIT) - FITUR BARU
    public function edit($id)
    {
        $review = Review::with('product')->findOrFail($id);

        // Pastikan yang edit adalah pemilik ulasan
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit ulasan ini.');
        }

        return view('reviews.edit', compact('review'));
    }

    // 4. UPDATE ULASAN (UPDATE) - FITUR BARU
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'required|string'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('products.show', $review->product_id)
                ->with('success', 'Ulasan berhasil diperbarui!');
    }

    // 5. HAPUS ULASAN (DESTROY) - FITUR BARU
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $productId = $review->product_id; // Simpan ID produk sebelum dihapus untuk redirect
        $review->delete();

        return redirect()->route('products.show', $productId)
                ->with('success', 'Ulasan berhasil dihapus.');
    }
}