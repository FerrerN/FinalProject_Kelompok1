@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold m-0 text-danger"><i class="bi bi-cart-check-fill me-2"></i> Checkout Keranjang</h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('transactions.checkout_process') }}" method="POST">
                        @csrf

                        {{-- === BAGIAN 1: DAFTAR BARANG (BISA BEDA TOKO) === --}}
                        <h6 class="fw-bold mb-3">Barang yang akan dibeli:</h6>
                        
                        <div class="list-group mb-4">
                            @foreach($carts as $cart)
                                <div class="list-group-item p-3 border rounded mb-2 bg-light">
                                    <div class="d-flex align-items-center">
                                        {{-- Gambar Produk --}}
                                        <img src="{{ $cart->product->url_gambar ?? 'https://via.placeholder.com/60' }}" 
                                             class="rounded me-3 border" width="60" height="60" style="object-fit:cover">
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="fw-bold mb-1">{{ $cart->product->nama_barang }}</h6>
                                                <span class="text-muted small"><i class="bi bi-shop"></i> {{ $cart->product->user->name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted">Qty: {{ $cart->quantity }} x Rp {{ number_format($cart->product->harga) }}</small>
                                                <span class="fw-bold text-danger">Rp {{ number_format($cart->product->harga * $cart->quantity) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total Harga Global --}}
                        <div class="alert alert-secondary d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold">Total Pembayaran:</span>
                            <span class="fs-5 fw-bold text-danger">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>

                        <hr>

                        {{-- === BAGIAN 2: FORM PENGIRIMAN (SATU UNTUK SEMUA) === --}}
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Tanggal Pengiriman</label>
                                <input type="date" name="shipping_date" class="form-control @error('shipping_date') is-invalid @enderror" 
                                       value="{{ old('shipping_date') }}" required>
                                <small class="text-muted">Tanggal ini berlaku untuk semua item di atas.</small>
                                
                                @error('shipping_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Catatan Tambahan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="1" placeholder="Pesan untuk penjual..."></textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger py-3 fw-bold rounded-pill">
                                <i class="bi bi-shield-lock-fill me-2"></i> Konfirmasi & Bayar Semua
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-light rounded-pill">Kembali ke Keranjang</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection