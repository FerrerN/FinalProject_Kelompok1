@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-4"><i class="bi bi-bag-check text-danger"></i> Konfirmasi Checkout Keranjang</h3>

            <!-- Alert API -->
            <div id="api-alert" style="display: none;" class="alert mb-4"></div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('transactions.cart_submit') }}" method="POST" id="checkout-form">
                @csrf
                
                <!-- Daftar Barang -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Daftar Barang di Keranjang</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carts as $cart)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $cart->product->url_gambar ?? 'https://via.placeholder.com/50' }}" 
                                                 class="rounded me-3" width="50" height="50" style="object-fit:cover;">
                                            <div>
                                                <div class="fw-bold">{{ $cart->product->nama_barang }}</div>
                                                <small class="text-muted">Rp {{ number_format($cart->product->harga) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $cart->quantity }}</td>
                                    <td class="text-end pe-4">Rp {{ number_format($cart->product->harga * $cart->quantity) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold pt-3">Total Pembayaran</td>
                                    <td class="text-end pe-4 fw-bold text-danger fs-5 pt-3">Rp {{ number_format($totalPayment) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Form Pengiriman -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Informasi Pengiriman</h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Pengiriman <span class="text-danger">*</span></label>
                            <input type="date" name="shipping_date" id="shipping_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            <div class="form-text">Pilih tanggal untuk mengecek ketersediaan kurir.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Tambahan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Pesan untuk penjual..."></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" id="btn-submit" class="btn btn-danger fw-bold py-3">
                                <i class="bi bi-credit-card-2-front me-2"></i> Proses Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT VALIDASI API HARI LIBUR -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('shipping_date');
        const alertBox = document.getElementById('api-alert');
        const btnSubmit = document.getElementById('btn-submit');

        // Fungsi cek tanggal
        function checkDate(dateVal) {
            // Disable tombol saat checking
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memeriksa...';

            fetch(`/api/shipping-check?date=${dateVal}`)
                .then(response => response.json())
                .then(data => {
                    // Reset Alert
                    alertBox.style.display = 'block';
                    alertBox.className = data.status === 'available' ? 'alert alert-success' : 'alert alert-danger';
                    
                    let icon = data.status === 'available' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
                    alertBox.innerHTML = `<i class="bi ${icon} me-2"></i> ${data.message}`;

                    // Kunci/Buka Tombol
                    if (data.status === 'unavailable') {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = 'Pengiriman Tidak Tersedia';
                        btnSubmit.classList.replace('btn-danger', 'btn-secondary');
                    } else {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="bi bi-credit-card-2-front me-2"></i> Proses Pembayaran';
                        btnSubmit.classList.replace('btn-secondary', 'btn-danger');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Proses Pembayaran (Offline Mode)';
                });
        }

        // Cek saat pertama load (hari ini)
        checkDate(dateInput.value);

        // Cek saat tanggal berubah
        dateInput.addEventListener('change', function() {
            checkDate(this.value);
        });
    });
</script>
@endsection