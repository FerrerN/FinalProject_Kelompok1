<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http; 
use Barryvdh\DomPDF\Facade\Pdf;      

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

    // 3. PROSES SIMPAN TRANSAKSI (DENGAN VALIDASI API HARI LIBUR)
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_date' => 'required|date',
        ]);

        // --- MULAI INTEGRASI API HARI LIBUR (SESUAI PROPOSAL) ---
        // Kita ambil tahun dari tanggal yang diinput user
        $year = date('Y', strtotime($request->shipping_date));
        
        // Panggil API Hari Libur Nasional
        $response = Http::get("https://api-harilibur.vercel.app/api?year={$year}");
        
        if ($response->successful()) {
            $holidays = $response->json();
            $selectedDate = date('Y-m-d', strtotime($request->shipping_date));

            foreach ($holidays as $holiday) {
                // Cek apakah tanggal pilihan user sama dengan hari libur nasional
                if ($holiday['holiday_date'] == $selectedDate && $holiday['is_national_holiday']) {
                    // Jika ya, kembalikan error
                    return back()->withErrors([
                        'shipping_date' => "Pengiriman tidak bisa dilakukan pada hari libur nasional: " . $holiday['holiday_name']
                    ])->withInput();
                }
            }
        }
        // --- SELESAI INTEGRASI API ---

        $product = Product::findOrFail($request->product_id);

        // Cek Stok
        if ($product->stok < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak cukup!']);
        }

        // Buat Transaksi
        Transaction::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
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

    // 6. LIHAT INVOICE (Tampilan HTML Biasa)
    public function printInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product.user'])->findOrFail($id);
        
        if (Auth::id() !== $transaction->user_id && Auth::id() !== $transaction->product->user_id) {
            abort(403, 'Akses ditolak');
        }

        return view('transactions.invoice', compact('transaction'));
    }

    // 7. DOWNLOAD PDF INVOICE (FITUR BARU SESUAI PROPOSAL)
    public function exportInvoice($id)
    {
        $transaction = Transaction::with(['user', 'product.user'])->findOrFail($id);

        // Validasi Akses
        if (Auth::id() !== $transaction->user_id && Auth::id() !== $transaction->product->user_id) {
            abort(403, 'Akses ditolak');
        }

        // Load View PDF (Pastikan file transactions/invoice_pdf.blade.php sudah dibuat)
        $pdf = Pdf::loadView('transactions.invoice_pdf', compact('transaction'));
        
        // Download File
        return $pdf->download('invoice-TRX-'.$transaction->id.'.pdf');
    }

   
}