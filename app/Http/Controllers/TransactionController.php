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

    // 2. FORM BELI LANGSUNG
    public function create()
    {
        $products = Product::where('status', 'aktif')->get();
        return view('transactions.create', compact('products'));
    }

    // Proses Simpan Transaksi Beli Langsung
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
            return back()->withErrors(['quantity' => 'Stok barang tidak mencukupi!']);
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
            'notes' => $request->notes . $holidayCheck['note'] 
        ]);

        // Kurangi Stok & Generate Token Midtrans
        $product->decrement('stok', $request->quantity);
        $this->generateMidtransToken($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    // =========================================================================
    // 3. FITUR CHECKOUT DARI KERANJANG (CART)
    // =========================================================================

    // [FIX] Menampilkan Halaman Konfirmasi Tanggal untuk Keranjang
    public function viewCartCheckout()
    {
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $totalPayment = 0;
        foreach ($carts as $cart) {
            $totalPayment += $cart->product->harga * $cart->quantity;
        }

        // Return view khusus di folder carts
        return view('carts.checkout', compact('carts', 'totalPayment'));
    }

    // Proses Simpan Transaksi dari Keranjang (Bulk Checkout)
    public function checkoutCart(Request $request)
    {
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->with('product')->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Keranjang Anda kosong.');
        }

        // Validasi Tanggal Pengiriman
        $request->validate([
            'shipping_date' => 'required|date|after_or_equal:today',
        ], [
            'shipping_date.required' => 'Mohon pilih tanggal pengiriman terlebih dahulu.'
        ]);

        // Cek Hari Libur
        $holidayCheck = $this->checkHoliday($request->shipping_date);

        if ($holidayCheck['allowed'] === false) {
            return back()->with('error', $holidayCheck['reason'])->withInput();
        }

        // Loop setiap item keranjang menjadi transaksi terpisah
        foreach ($carts as $cart) {
            if ($cart->product->stok < $cart->quantity) {
                return back()->with('error', "Stok {$cart->product->nama_barang} tidak mencukupi.");
            }

            $transaction = Transaction::create([
                'user_id' => $userId,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'total_price' => $cart->product->harga * $cart->quantity,
                'shipping_date' => $request->shipping_date,
                'status' => 'pending',
                'notes' => "Checkout Keranjang. " . $request->notes . " " . $holidayCheck['note'],
            ]);

            $cart->product->decrement('stok', $cart->quantity);
            $this->generateMidtransToken($transaction);
        }

        // Kosongkan Keranjang setelah sukses
        Cart::where('user_id', $userId)->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Checkout Keranjang Berhasil! Silakan bayar pesanan Anda.');
    }

    // =========================================================================
    // 4. MANAJEMEN TRANSAKSI (UPDATE & DELETE)
    // =========================================================================

    // Update Status (Khusus Penjual)
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->product->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah pesanan ini.');
        }

        $request->validate([
            'status' => 'required|in:paid,sent,completed,cancelled'
        ]);

        $transaction->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // Hapus Transaksi / Batalkan Pesanan
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Kembalikan stok jika pesanan belum selesai/dikirim
        if ($transaction->status != 'completed' && $transaction->status != 'sent') {
            $transaction->product->increment('stok', $transaction->quantity ?? 1);
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    // Export Invoice PDF
    public function exportInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);
        
        // Load view PDF (pastikan view ini ada)
        $pdf = Pdf::loadView('transactions.invoice', compact('transaction'));
        return $pdf->download('invoice-'.$transaction->id.'.pdf');
    }

    public function printInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product'])->findOrFail($id);
        return view('transactions.invoice', compact('transaction'));
    }

    // =========================================================================
    // 5. API & HELPER FUNCTIONS
    // =========================================================================

    // API Publik untuk Cek Ketersediaan Tanggal (AJAX Frontend)
    public function checkDateAvailability(Request $request)
    {
        $date = $request->query('date');
        
        if (!$date) {
            return response()->json(['status' => 'error', 'message' => 'Tanggal wajib diisi']);
        }

        $check = $this->checkHoliday($date);
        
        return response()->json([
            'status' => $check['allowed'] ? 'available' : 'unavailable',
            'message' => $check['allowed'] ? ($check['note'] ?: 'Pengiriman tersedia') : $check['reason'],
            'note' => $check['note']
        ]);
    }

    // Helper: Logika Deteksi Hari Libur (API External)
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
            // Timeout 5 detik untuk menghindari loading lama
            $response = Http::timeout(5)->get("https://api-harilibur.vercel.app/api?year={$year}");
            
            if ($response->successful()) {
                $holidays = $response->json();
                
                foreach ($holidays as $holiday) {
                    if (!$holiday['is_national_holiday']) continue;

                    // LOGIKA 1: Cek Tanggal Merah (Block)
                    if ($holiday['holiday_date'] === $inputDateStr) {
                        $result['allowed'] = false;
                        $result['reason'] = "PENGIRIMAN DITUTUP: {$holiday['holiday_name']}";
                        return $result; 
                    }

                    // LOGIKA 2: Cek H-3 (Warning)
                    $holidayDate = Carbon::parse($holiday['holiday_date']);
                    $shippingDate = Carbon::parse($inputDateStr);
                    $diff = $shippingDate->diffInDays($holidayDate, false);

                    if ($diff == 3) {
                        $result['note'] .= " [INFO TRAFFIC: H-3 sebelum Libur {$holiday['holiday_name']}.]";
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback Manual jika API Down (Agar demo tetap jalan)
            $manualHolidays = [
                '2025-12-25' => 'Hari Natal',
                '2025-01-01' => 'Tahun Baru Masehi',
            ];
            if (array_key_exists($inputDateStr, $manualHolidays)) {
                $result['allowed'] = false;
                $result['reason'] = "PENGIRIMAN DITUTUP (OFFLINE): " . $manualHolidays[$inputDateStr];
            }
        }

        return $result;
    }

    // Helper: Generate Token Midtrans
    private function generateMidtransToken($transaction)
    {
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
            // Abaikan error midtrans jika config belum lengkap
        }
    }
}