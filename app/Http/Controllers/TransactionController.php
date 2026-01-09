<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http; 
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Config;
use Midtrans\Snap;


class TransactionController extends Controller
{
    // 1. DAFTAR TRANSAKSI
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'penjual') {
            $transactions = Transaction::whereHas('product', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['product', 'user'])->latest()->get();
        } else {
            $transactions = Transaction::where('user_id', $user->id)
                ->with('product.user')
                ->latest()->get();
        }

        return view('transactions.index', compact('transactions'));
    }

    // 2. FORM BELI LANGSUNG
    public function create()
    {
        $products = Product::where('status', 'aktif')->get();
        return view('transactions.create', compact('products'));
    }

    // 3. PROSES SIMPAN (API HARI LIBUR)
    public function store(Request $request)
    {
        // A. Validasi Input
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

        // B. INTEGRASI API HARI LIBUR (Mode: Peringatan)
        $holidayNote = "";
        $year = date('Y', strtotime($request->shipping_date));
        
        try {
            $response = Http::timeout(3)->get("https://api-harilibur.vercel.app/api?year={$year}");
            
            if ($response->successful()) {
                $holidays = $response->json();
                $selectedDate = date('Y-m-d', strtotime($request->shipping_date));

                foreach ($holidays as $holiday) {
                    if ($holiday['holiday_date'] == $selectedDate && $holiday['is_national_holiday']) {
                        // Jangan di-return error, tapi tambahkan ke catatan
                        $holidayNote = " [INFO: Estimasi pengiriman jatuh pada hari libur nasional ({$holiday['holiday_name']}). Kemungkinan ada keterlambatan.]";
                        break; 
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika API error, abaikan saja biar transaksi tetap jalan
        }

        // C. Simpan Transaksi ke Database
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $product->harga * $request->quantity,
            'shipping_date' => $request->shipping_date,
            'status' => 'pending',
            'notes' => $request->notes . $holidayNote // Gabungkan catatan user + info libur
        ]);

        // Kurangi Stok
        $product->decrement('stok', $request->quantity);

        // D. INTEGRASI MIDTRANS (Minta Token Pembayaran)
        // Set konfigurasi
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Siapkan parameter Midtrans
        $midtransParams = [
            'transaction_details' => [
                'order_id' => 'TRX-' . $transaction->id . '-' . time(), // ID Unik
                'gross_amount' => (int) $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
            ],
            'item_details' => [
                [
                    'id' => $product->id,
                    'price' => (int) $product->harga,
                    'quantity' => (int) $request->quantity,
                    'name' => substr($product->nama_barang, 0, 50)
                ]
            ]
        ];

        try {
            // Minta Snap Token
            $snapToken = Snap::getSnapToken($midtransParams);
            
            // Simpan Token ke Database
            $transaction->snap_token = $snapToken;
            $transaction->save();
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        }

        // E. Redirect ke Halaman Detail (Untuk Bayar)
        // Bukan ke index, tapi ke show agar tombol bayar muncul
        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', 'Pesanan dibuat! Silakan selesaikan pembayaran.');
    }

    // 4. DETAIL TRANSAKSI (Halaman Bayar)
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);
        
        // Security Check
        if (Auth::id() !== $transaction->user_id && Auth::id() !== $transaction->product->user_id) {
            abort(403);
        }

        return view('transactions.show', compact('transaction'));
    }

    // 5. UPDATE STATUS (Penjual)
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->product->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah pesanan ini.');
        }

        $request->validate([
            'status' => 'required|in:pending,dikirim,selesai,batal'
        ]);

        $transaction->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // 6. BATALKAN PESANAN
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status != 'batal') {
            $transaction->product->increment('stok', $transaction->quantity ?? 1);
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi dihapus.');
    }

    // 7. EXPORT PDF
    public function printInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product.user'])->findOrFail($id);

        // Security Check: Pastikan yang lihat cuma Pembeli atau Penjual
        if (Auth::id() !== $transaction->user_id && Auth::id() !== $transaction->product->user_id) {
            abort(403, 'Akses ditolak');
        }

        return view('transactions.invoice', compact('transaction'));
    }
    
    // 8. SUKSES BAYAR (Callback Sederhana)
    public function success()
    {
        return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil!');
    }

    // --- FITUR BARU: CHECKOUT DARI KERANJANG ---
    public function checkoutCart(Request $request)
    {
        // 1. Ambil Data Keranjang User
        $userID = Auth::id();
        $carts = \App\Models\Cart::where('user_id', $userID)->with('product')->get();

        if ($carts->isEmpty()) {
            return back()->withErrors(['error' => 'Keranjang masih kosong!']);
        }

        // 2. Validasi Tanggal Pengiriman (Dari Input Form Keranjang)
        $shippingDate = $request->input('shipping_date', now()->addDays(3));

        // 3. Loop setiap barang di keranjang -> Pindahkan ke Transaksi
        foreach ($carts as $cart) {
            
            // Cek Stok Dulu
            if ($cart->product->stok < $cart->quantity) {
                return back()->withErrors(['error' => 'Stok untuk ' . $cart->product->nama_barang . ' tidak mencukupi!']);
            }

            // Buat Transaksi
            $transaction = Transaction::create([
                'user_id'       => $userID,
                'product_id'    => $cart->product_id,
                'quantity'      => $cart->quantity,
                'total_price'   => $cart->product->harga * $cart->quantity,
                'status'        => 'pending',
                'shipping_date' => $shippingDate,
                'notes'         => 'Checkout dari Keranjang',
            ]);

            // GENERATE TOKEN MIDTRANS (Agar bisa langsung dibayar per item)
            // (Kita copy logika dari fungsi store sebelumnya agar rapi)
            $this->generateMidtransToken($transaction);

            // Kurangi Stok Produk
            $cart->product->decrement('stok', $cart->quantity);
        }

        // 4. Kosongkan Keranjang
        \App\Models\Cart::where('user_id', $userID)->delete();

        // 5. Redirect ke Riwayat Transaksi
        return redirect()->route('transactions.index')->with('success', 'Checkout berhasil! Silakan lakukan pembayaran.');
    }

    // HELPER: Fungsi Generate Token Midtrans (Agar kodingan tidak duplikat)
    // Pastikan library Midtrans/Config & Midtrans/Snap sudah di-use di atas
    private function generateMidtransToken($transaction)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'TRX-' . $transaction->id . '-' . time(),
                'gross_amount' => (int) $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->snap_token = $snapToken;
            $transaction->save();
        } catch (\Exception $e) {
            // Abaikan error midtrans agar transaksi tetap tersimpan
        }
    }
}