<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http; // <--- WAJIB: Jangan lupa import ini!

class ReviewController extends Controller
{
    // 1. FORM TULIS ULASAN (CREATE)
    public function create($transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)
                        ->where('user_id', Auth::id())
                        ->where('status', 'selesai')
                        ->firstOrFail();

        $existingReview = Review::where('transaction_id', $transactionId)->first();
        if ($existingReview) {
            return redirect()->route('products.show', $transaction->product_id)
                    ->with('error', 'Anda sudah mengulas transaksi ini.');
        }

        return view('reviews.create', compact('transaction'));
    }

    // 2. SIMPAN ULASAN (STORE) - DENGAN API FILTER
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'required|string|min:3'
        ]);

       // DI DALAM FUNCTION STORE & UPDATE (Logika API nya saja)

    // ...
    // --- INTEGRASI API: FILTER KATA KASAR ---
    $komentarBersih = $request->comment;
    
    try {
        $kataKasarIndo = 'anjing,babi,bangsat,tolol,goblok,bodoh,setan,kampret';

        // TAMBAHKAN ->withoutVerifying() ATAU option verify false
        $response = Http::withoutVerifying() // <--- SOLUSI DISINI (Bypass SSL)
            ->timeout(5)
            ->get('https://www.purgomalum.com/service/json', [
                'text' => $request->comment,
                'add'  => $kataKasarIndo,
                'fill_char' => '*'
            ]);

        if ($response->successful()) {
            $komentarBersih = $response->json()['result'];
        }
    } catch (\Exception $e) {
        // Silent fail
    }
    // ----------------------------------------
    // ...
        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'transaction_id' => $request->transaction_id,
            'rating' => $request->rating,
            'comment' => $komentarBersih // Simpan hasil filter API
        ]);

        return redirect()->route('products.show', $request->product_id)
                ->with('success', 'Ulasan berhasil dikirim!');
    }

    // 3. FORM EDIT ULASAN (EDIT)
    public function edit($id)
    {
        $review = Review::with('product')->findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit ulasan ini.');
        }

        return view('reviews.edit', compact('review'));
    }

    // 4. UPDATE ULASAN (UPDATE) - DENGAN API FILTER
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'required|string|min:3'
        ]);

         // --- INTEGRASI API: FILTER KATA KASAR ---
    $komentarBersih = $request->comment;
    
    try {
        $kataKasarIndo = 'anjing,babi,bangsat,tolol,goblok,bodoh,setan,kampret';

        // TAMBAHKAN ->withoutVerifying() ATAU option verify false
        $response = Http::withoutVerifying() // <--- SOLUSI DISINI (Bypass SSL)
            ->timeout(5)
            ->get('https://www.purgomalum.com/service/json', [
                'text' => $request->comment,
                'add'  => $kataKasarIndo,
                'fill_char' => '*'
            ]);

        if ($response->successful()) {
            $komentarBersih = $response->json()['result'];
        }
    } catch (\Exception $e) {
        // Silent fail
    }
    // ----------------------------------------

        $review->update([
            'rating' => $request->rating,
            'comment' => $komentarBersih // Update dengan hasil filter
        ]);

        return redirect()->route('products.show', $review->product_id)
                ->with('success', 'Ulasan berhasil diperbarui!');
    }

    // 5. HAPUS ULASAN (DESTROY)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $productId = $review->product_id; 
        $review->delete();

        return redirect()->route('products.show', $productId)
                ->with('success', 'Ulasan berhasil dihapus.');
    }
}