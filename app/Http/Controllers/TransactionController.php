<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http; 
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Config;
use Midtrans\Snap;
use Carbon\Carbon;

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

    // 2. FORM BELI LANGSUNG (Updated)
    public function create(Request $request)
    {
        // 1. Ambil data produk semua (untuk jaga-jaga)
        $products = Product::where('status', 'aktif')->get();
        
        // 2. Cek apakah ada parameter 'product_id' dari URL?
        $selectedProduct = null;
        if ($request->has('product_id')) {
            $selectedProduct = Product::find($request->product_id);
        }

        return view('transactions.create', compact('products', 'selectedProduct'));
    }

    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_date' => 'required|date|after_or_equal:today',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cek Stok
        if ($product->stok < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok barang tidak mencukupi!']);
        }

        // --- [LOGIKA UTAMA] CEK API HARI LIBUR ---
        $holidayCheck = $this->checkHoliday($request->shipping_date);

        // JIKA TANGGAL MERAH -> TOLAK PESANAN
        if ($holidayCheck['allowed'] === false) {
            // Kembali ke form dengan pesan error
            return back()
                ->withErrors(['shipping_date' => $holidayCheck['reason']])
                ->withInput(); 
        }

        // Jika lolos, simpan transaksi
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $product->harga * $request->quantity,
            'shipping_date' => $request->shipping_date,
            'status' => 'pending',
            'notes' => $request->notes . " " . $holidayCheck['note'] 
        ]);

        $product->decrement('stok', $request->quantity);
        $this->generateMidtransToken($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    // 3. CHECKOUT KERANJANG
    // A. TAMPILKAN HALAMAN CHECKOUT (Review Order)
    public function checkoutPage()
    {
        $userId = Auth::id();
        // Ambil semua item di keranjang user
        $carts = Cart::where('user_id', $userId)->with(['product.user'])->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Hitung Total Harga Keseluruhan
        $grandTotal = 0;
        foreach($carts as $cart) {
            $grandTotal += $cart->product->harga * $cart->quantity;
        }

        return view('transactions.checkout', compact('carts', 'grandTotal'));
    }

    // B. PROSES SIMPAN TRANSAKSI (BULK)
    public function checkoutCart(Request $request)
    {
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->with('product')->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Keranjang Anda kosong.');
        }

        // 1. Validasi Tanggal
        $request->validate([
            'shipping_date' => 'required|date|after_or_equal:today',
        ], [
            'shipping_date.required' => 'Mohon pilih tanggal pengiriman.'
        ]);

        // 2. Cek API Hari Libur
        $holidayCheck = $this->checkHoliday($request->shipping_date);
        
        // Jika Tanggal Merah -> Tolak
        if ($holidayCheck['allowed'] === false) {
            return back()->withErrors(['shipping_date' => $holidayCheck['reason']])->withInput();
        }

        // 3. Loop Item Keranjang & Simpan Transaksi
        foreach ($carts as $cart) {
            // Cek Stok per item
            if ($cart->product->stok < $cart->quantity) {
                return back()->with('error', "Stok {$cart->product->nama_barang} tidak mencukupi.");
            }

            // Buat Transaksi
            $transaction = Transaction::create([
                'user_id' => $userId,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'total_price' => $cart->product->harga * $cart->quantity,
                'shipping_date' => $request->shipping_date, // Tanggal sama untuk semua
                'status' => 'pending',
                'notes' => "Checkout Keranjang. " . $request->notes . " " . $holidayCheck['note'],
            ]);

            // Kurangi Stok
            $cart->product->decrement('stok', $cart->quantity);
            
            // Generate Token Midtrans (Per transaksi)
            $this->generateMidtransToken($transaction);
        }

        // 4. Kosongkan Keranjang
        Cart::where('user_id', $userId)->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Checkout Berhasil! Silakan lakukan pembayaran untuk setiap item.');
    }

    // 4. UPDATE & DELETE
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->product->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah pesanan ini.');
        }

        $request->validate([
            'status' => 'required|in:paid,sent,completed,cancelled,pending'
        ]);

        $transaction->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status != 'completed' && $transaction->status != 'sent') {
            $transaction->product->increment('stok', $transaction->quantity ?? 1);
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    // 5. INVOICE
    public function exportInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);
        $pdf = Pdf::loadView('transactions.invoice', compact('transaction'));
        return $pdf->download('invoice-'.$transaction->id.'.pdf');
    }

    public function printInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);
        return view('transactions.invoice', compact('transaction'));
    }

    // =========================================================================
    // 6. HELPER: CEK API HARI LIBUR (BLOCKING MODE)
    // =========================================================================
    private function checkHoliday($dateInput)
    {
        $result = [
            'allowed' => true,
            'reason' => '',
            'note' => ''
        ];

        $inputDateStr = Carbon::parse($dateInput)->format('Y-m-d');
        $year = Carbon::parse($dateInput)->format('Y');
        
        try {
            // Panggil API dengan timeout 5 detik
            $response = Http::withoutVerifying()
                ->timeout(5) 
                ->get("https://api-harilibur.vercel.app/api?year={$year}");
            
            if ($response->successful()) {
                $holidays = $response->json();
                
                foreach ($holidays as $holiday) {
                    // Hanya proses hari libur nasional
                    if (!$holiday['is_national_holiday']) continue;

                    // LOGIKA 1: BLOCK JIKA TANGGAL MERAH
                    // Jika tanggal input SAMA PERSIS dengan tanggal merah -> BLOKIR
                    if ($holiday['holiday_date'] === $inputDateStr) {
                        $result['allowed'] = false;
                        $result['reason'] = "MAAF, PENGIRIMAN LIBUR: {$holiday['holiday_name']}";
                        return $result; // Langsung stop dan kembalikan status DITOLAK
                    }

                    // LOGIKA 2: WARNING JIKA H-3
                    $holidayDate = Carbon::parse($holiday['holiday_date']);
                    $shippingDate = Carbon::parse($inputDateStr);
                    $diff = $shippingDate->diffInDays($holidayDate, false);

                    if ($diff > 0 && $diff <= 3) {
                        $result['note'] .= " [INFO: Pengiriman mungkin terlambat karena H-{$diff} libur {$holiday['holiday_name']}]";
                    }
                }
            }
        } catch (\Exception $e) {
            // Silent Fail: Jika API mati, biarkan tetap bisa checkout
        }

        return $result;
    }

    // Helper: Midtrans
    private function generateMidtransToken($transaction)
    {
        if (!env('MIDTRANS_SERVER_KEY')) {
            return; 
        }

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'TRX-' . $transaction->id . '-' . time(),
                'gross_amount' => (int) $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => $transaction->product_id,
                    'price' => (int) $transaction->product->harga,
                    'quantity' => (int) $transaction->quantity,
                    'name' => substr($transaction->product->nama_barang, 0, 40)
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->snap_token = $snapToken;
            $transaction->save();
        } catch (\Exception $e) {
        }
    }
}