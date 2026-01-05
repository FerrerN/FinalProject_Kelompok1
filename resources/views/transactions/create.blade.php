<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - FJB Tel-U</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-telu {
            background: linear-gradient(to right, #b91d47, #ee395f);
        }
        .card-form {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.1);
        }
        .btn-telu {
            background: linear-gradient(to right, #b91d47, #ee395f);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-telu:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(185, 29, 71, 0.3);
        }
        /* Sticky Summary di sebelah kanan */
        .summary-card {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-telu shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i> FJB Tel-U
            </a>
            <div class="ms-auto">
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3">
                    <i class="bi bi-x-lg me-1"></i> Batal
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="row">
            <div class="col-lg-8 mb-4">
                <h4 class="fw-bold mb-3 text-dark">Detail Pesanan</h4>
                
                <div class="card card-form bg-white p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Pilih Produk</label>
                            <select name="product_id" id="productSelect" class="form-select" required>
                                <option value="" data-price="0" selected disabled>-- Silakan Pilih Barang --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->harga }}">
                                        {{ $product->nama_barang }} - (Stok: {{ $product->stok }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">Pastikan stok barang tersedia.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Jumlah (Qty)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                                    <input type="number" name="quantity" id="quantityInput" class="form-control text-center" value="1" min="1" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Rencana Kirim / COD</label>
                                <input type="date" name="shipping_date" class="form-control" required 
                                       value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Tolong packing kayu, atau COD di depan Gedung TULT"></textarea>
                        </div>

                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-form bg-white p-4 summary-card">
                    <h5 class="fw-bold mb-3">Ringkasan Pembayaran</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Harga Satuan</span>
                        <span class="fw-bold" id="priceDisplay">Rp 0</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Jumlah</span>
                        <span class="fw-bold" id="qtyDisplay">x 1</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <span class="fw-bold text-dark">Total Bayar</span>
                        <span class="fw-bold text-danger fs-4" id="totalDisplay">Rp 0</span>
                    </div>

                    <button type="button" onclick="submitForm()" class="btn btn-primary btn-telu w-100 text-white shadow-sm">
                        <i class="bi bi-bag-check me-2"></i> Buat Pesanan
                    </button>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-lock"></i> Transaksi Aman & Terpercaya
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const productSelect = document.getElementById('productSelect');
        const quantityInput = document.getElementById('quantityInput');
        const priceDisplay = document.getElementById('priceDisplay');
        const qtyDisplay = document.getElementById('qtyDisplay');
        const totalDisplay = document.getElementById('totalDisplay');

        function formatRupiah(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        function calculateTotal() {
            // Ambil harga dari atribut data-price di option yang dipilih
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const qty = parseInt(quantityInput.value) || 1;

            const total = price * qty;

            // Update Tampilan
            priceDisplay.innerText = formatRupiah(price);
            qtyDisplay.innerText = 'x ' + qty;
            totalDisplay.innerText = formatRupiah(total);
        }

        function adjustQty(change) {
            let currentQty = parseInt(quantityInput.value) || 1;
            let newQty = currentQty + change;
            if (newQty < 1) newQty = 1;
            quantityInput.value = newQty;
            calculateTotal();
        }

        function submitForm() {
            document.getElementById('checkoutForm').submit();
        }

        // Event Listeners (Jalankan fungsi saat ada perubahan)
        productSelect.addEventListener('change', calculateTotal);
        quantityInput.addEventListener('input', calculateTotal);
    </script>
</body>
</html>