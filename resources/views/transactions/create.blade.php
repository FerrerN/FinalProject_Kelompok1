@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold m-0 text-danger"><i class="bi bi-bag-check-fill me-2"></i> Konfirmasi Pembelian</h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf

                        {{-- === BAGIAN 1: PRODUK === --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Produk yang Dibeli</label>
                            
                            @if(isset($selectedProduct))
                                {{-- JIKA DARI TOMBOL BELI SEKARANG (TAMPILKAN KARTU PRODUK) --}}
                                <div class="d-flex align-items-center p-3 border rounded bg-light">
                                    <img src="{{ $selectedProduct->url_gambar ?? 'https://via.placeholder.com/80' }}" 
                                         class="rounded me-3" width="80" height="80" style="object-fit:cover">
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ $selectedProduct->nama_barang }}</h5>
                                        <p class="text-danger fw-bold mb-0">Rp {{ number_format($selectedProduct->harga) }}</p>
                                        <small class="text-muted">Stok Tersedia: {{ $selectedProduct->stok }}</small>
                                    </div>
                                </div>
                                {{-- Input Hidden agar ID produk tetap terkirim --}}
                                <input type="hidden" name="product_id" value="{{ $selectedProduct->id }}">
                            @else
                                {{-- JIKA AKSES MANUAL (TAMPILKAN DROPDOWN) --}}
                                <select name="product_id" class="form-select">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->nama_barang }} - Rp {{ number_format($product->harga) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="row">
                            {{-- === BAGIAN 2: JUMLAH === --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Jumlah (Qty)</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>

                            {{-- === BAGIAN 3: TANGGAL PENGIRIMAN (API CHECK) === --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Tanggal Pengiriman</label>
                                <input type="date" name="shipping_date" class="form-control @error('shipping_date') is-invalid @enderror" 
                                       value="{{ old('shipping_date') }}" required>
                                <small class="text-muted">Pilih tanggal untuk cek ketersediaan.</small>
                                
                                @error('shipping_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Titip di satpam..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger py-3 fw-bold rounded-pill">
                                <i class="bi bi-shield-lock-fill me-2"></i> Bayar Sekarang
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-light rounded-pill">Batal</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection