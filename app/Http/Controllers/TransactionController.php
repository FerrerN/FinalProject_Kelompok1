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
use Carbon\Carbon;

class TransactionController extends Controller
{
    // 1. DAFTAR TRANSAKSI
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'penjual') {
            // Penjual melihat pesanan yang masuk untuk produk mereka
            $transactions = Transaction::whereHas('product', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['product', 'user'])->latest()->get();
        } else {
            // Pembeli melihat riwayat belanja mereka
            $transactions = Transaction::where('user_id', $user->id)
                ->with('product.user')
                ->latest()->get();
        }

        return view('transactions.index', compact('transactions'));
    }

    // 2. BELI LANGSUNG (SATUAN)
    public function create(Request $request)
    {
        // 1. Ambil data produk semua (untuk jaga-jaga/dropdown manual)
        $products = Product::where('status', 'aktif')->get();
        
        // 2. Cek apakah ada parameter 'product_id' dari URL (Klik tombol Beli)
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
            return back()
                ->withErrors(['shipping_date' => $holidayCheck['reason']])
                ->withInput(); 
        }

        // Simpan Transaksi
        Transaction::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $product->harga * $request->quantity,
            'shipping_date' => $request->shipping_date,
            'status' => 'pending',
            'notes' => $request->notes . " " . $holidayCheck['note'] 
        ]);

        // Kurangi Stok
        $product->decrement('stok', $request->quantity);

        return redirect()->route('transactions.index')
            ->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi penjual.');
    }

// 3. CHECKOUT DARI KERANJANG (BANYAK BARANG)
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
            Transaction::create([
                'user_id' => $userId,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'total_price' => $cart->product->harga * $cart->quantity,
                'shipping_date' => $request->shipping_date, // Tanggal sama untuk semua item
                'status' => 'pending',
                'notes' => "Checkout Keranjang. " . $request->notes . " " . $holidayCheck['note'],
            ]);

            // Kurangi Stok
            $cart->product->decrement('stok', $cart->quantity);
        }

        // 4. Kosongkan Keranjang
        Cart::where('user_id', $userId)->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Checkout Berhasil! Semua pesanan telah dibuat.');
    }

    // 4. UPDATE & DELETE
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Pastikan hanya penjual pemilik produk yang bisa update status
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
        
        // Kembalikan stok jika pesanan belum selesai/dikirim saat dihapus
        if ($transaction->status != 'completed' && $transaction->status != 'sent') {
            $transaction->product->increment('stok', $transaction->quantity ?? 1);
        }

        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

 
    // 5. INVOICE (EXPORT & PRINT)
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
    // 6. HELPER: CEK API HARI LIBUR
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
                    if ($holiday['holiday_date'] === $inputDateStr) {
                        $result['allowed'] = false;
                        $result['reason'] = "MAAF, PENGIRIMAN LIBUR: {$holiday['holiday_name']}";
                        return $result; // Langsung stop dan kembalikan status DITOLAK
                    }
                }
            }
        } catch (\Exception $e) {
            // Silent Fail: Jika API mati, biarkan tetap bisa checkout
        }

        return $result;
    }
}