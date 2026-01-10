<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http; // Wajib import Http

class ReviewController extends Controller
{
    // 1. CREATE
    public function create($transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)
                        ->where('user_id', Auth::id())
                        ->whereIn('status', ['completed', 'selesai']) 
                        ->firstOrFail();

        $existingReview = Review::where('transaction_id', $transactionId)->first();
        if ($existingReview) {
            return redirect()->route('products.show', $transaction->product_id)
                    ->with('error', 'Anda sudah mengulas transaksi ini.');
        }

        return view('reviews.create', compact('transaction'));
    }

    // 2. STORE
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'rating' => 'required|integer|min:1|max:5', 
            'comment' => 'required|string|min:3'
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Akses ilegal');
        }

        // --- FILTER PROFANITY ---
        $komentarBersih = $this->filterProfanity($request->comment);

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $transaction->product_id,
            'transaction_id' => $transaction->id,
            'rating' => $request->rating,
            'comment' => $komentarBersih
        ]);

        return redirect()->route('products.show', $transaction->product_id)
                ->with('success', 'Ulasan berhasil dikirim!');
    }

    // 3. EDIT
    public function edit($id)
    {
        $review = Review::with('product')->findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reviews.edit', compact('review'));
    }

    // 4. UPDATE
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

        // --- FILTER PROFANITY ---
        $komentarBersih = $this->filterProfanity($request->comment);

        $review->update([
            'rating' => $request->rating,
            'comment' => $komentarBersih
        ]);

        return redirect()->route('products.show', $review->product_id)
                ->with('success', 'Ulasan berhasil diperbarui!');
    }

    // 5. DESTROY
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

    // ==========================================
    // HELPER: API FILTER (SUDAH DIPERBAIKI)
    // ==========================================
    private function filterProfanity($text)
    {
        // PERBAIKAN: Hanya masukkan maksimal 10 kata Indonesia.
        // Kata Inggris (shit, fuck, dll) TIDAK PERLU dimasukkan karena API sudah sensor otomatis.
        $kataIndo = 'anjing,babi,bangsat,tolol,goblok,bodoh,setan,kampret,sialan';

        try {
            $response = Http::withoutVerifying() // Bypass SSL
                ->timeout(5)
                ->get('https://www.purgomalum.com/service/json', [
                    'text' => $text,
                    'add'  => $kataIndo, // Kirim kata Indo saja
                    'fill_char' => '*'
                ]);

            if ($response->successful()) {
                // Ambil hasil yang sudah disensor
                $result = $response->json();
                if(isset($result['result'])) {
                    return $result['result'];
                }
            }
        } catch (\Exception $e) {
            // Jika API Error/Timeout, biarkan teks asli (Silent Fail)
            // Atau bisa return error message jika mau debugging lagi
        }

        return $text; // Kembalikan teks asli jika gagal
    }
}