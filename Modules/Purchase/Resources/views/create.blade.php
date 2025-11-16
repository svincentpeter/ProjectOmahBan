@extends('layouts.app')

@section('title', 'Tambah Pembelian')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Pembelian</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- LEFT COLUMN: Form Input --}}
                    <div class="col-lg-8">
                        {{-- Informasi Pembelian --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="cil-storage mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Informasi Pembelian</h5>
                                        <small class="text-muted">Lengkapi detail utama pembelian stok dari supplier</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Tanggal --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label small font-weight-semibold text-dark">
                                                Tanggal <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('date') is-invalid @enderror"
                                                name="date" id="date"
                                                value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                            @error('date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Reference (Auto-generate, readonly) --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference" class="form-label small font-weight-semibold text-dark">
                                                Reference <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="reference" id="reference"
                                                value="PB-{{ date('Ymd') }}-AUTO" readonly required>
                                            <small class="text-muted">Reference akan di-generate otomatis saat
                                                disimpan</small>
                                        </div>
                                    </div>

                                    {{-- Supplier --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="supplier_id"
                                                class="form-label small font-weight-semibold text-dark">
                                                Supplier <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('supplier_id') is-invalid @enderror"
                                                name="supplier_id" id="supplier_id" required>
                                                <option value="">Pilih Supplier</option>
                                                @foreach (\Modules\People\Entities\Supplier::all() as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                        {{ $supplier->supplier_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('supplier_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label small font-weight-semibold text-dark">
                                                Status <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('status') is-invalid @enderror"
                                                name="status" id="status" required>
                                                <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>
                                                    Pending
                                                </option>
                                                <option value="Completed"
                                                    {{ old('status') == 'Completed' ? 'selected' : '' }}>
                                                    Completed
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Pilih "Completed" jika stok langsung masuk ke
                                                gudang.</small>
                                        </div>
                                    </div>

                                    {{-- Payment Method --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_method"
                                                class="form-label small font-weight-semibold text-dark">
                                                Metode Pembayaran <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('payment_method') is-invalid @enderror"
                                                name="payment_method" id="payment_method" required>
                                                <option value="">Pilih Metode</option>
                                                <option value="Tunai"
                                                    {{ old('payment_method') == 'Tunai' ? 'selected' : '' }}>
                                                    Tunai
                                                </option>
                                                <option value="Transfer"
                                                    {{ old('payment_method') == 'Transfer' ? 'selected' : '' }}>
                                                    Transfer
                                                </option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Bank Name (Conditional) --}}
                                    <div class="col-md-12" id="bank_name_wrapper"
                                        style="display: {{ old('payment_method') == 'Transfer' ? 'block' : 'none' }};">
                                        <div class="mb-3">
                                            <label for="bank_name" class="form-label small font-weight-semibold text-dark">
                                                Nama Bank
                                            </label>
                                            <input type="text"
                                                class="form-control @error('bank_name') is-invalid @enderror"
                                                name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
                                                placeholder="Contoh: BCA, Mandiri, BRI">
                                            @error('bank_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Note --}}
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label for="note" class="form-label small font-weight-semibold text-dark">
                                                Catatan
                                            </label>
                                            <textarea class="form-control" name="note" id="note" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('note') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PRODUCTS SELECTION --}}
                        <div class="card shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="cil-list-rich mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Pilih Produk</h5>
                                        <small class="text-muted">Tambahkan produk yang dibeli dari supplier</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Pilih Produk --}}
                                <div class="row align-items-end">
                                    <div class="col-md-10">
                                        <div class="mb-3">
                                            <label for="product_id"
                                                class="form-label small font-weight-semibold text-dark">
                                                Produk
                                            </label>
                                            <select class="form-control" id="product_id">
                                                <option value="">Pilih Produk</option>
                                                @foreach (\Modules\Product\Entities\Product::all() as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-name="{{ $product->product_name }}"
                                                        data-code="{{ $product->product_code }}"
                                                        data-price="{{ $product->product_cost }}"
                                                        data-stock="{{ $product->product_quantity }}">
                                                        {{ $product->product_name }}
                                                        ({{ $product->product_code }})
                                                        - Stok:
                                                        {{ $product->product_quantity }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" id="add_product">
                                            <i class="cil-plus mr-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>

                                {{-- CART TABLE --}}
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="cart_table">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th width="15%">Qty</th>
                                                <th width="20%">Harga Satuan</th>
                                                <th width="20%">Subtotal</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart_body">
                                            <tr id="empty_cart">
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    Belum ada produk dipilih
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Summary --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm sticky-top" style="top: 90px;">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="cil-calculator mr-2 text-primary" style="font-size: 1.4rem;"></i>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Ringkasan Pembelian</h5>
                                        <small class="text-muted">Pastikan nilai pembayaran sudah sesuai</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Total Amount --}}
                                <div class="mb-3">
                                    <label class="form-label small font-weight-semibold text-dark">
                                        Total Pembelian
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control bg-light" id="total_display"
                                            value="0" readonly>
                                    </div>
                                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                                </div>

                                {{-- Paid Amount --}}
                                <div class="mb-3">
                                    <label for="paid_amount_display"
                                        class="form-label small font-weight-semibold text-dark">
                                        Jumlah Bayar <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text"
                                            class="form-control @error('paid_amount') is-invalid @enderror"
                                            id="paid_amount_display" value="{{ old('paid_amount', 0) }}">
                                    </div>
                                    <input type="hidden" name="paid_amount" id="paid_amount"
                                        value="{{ old('paid_amount', 0) }}">
                                    @error('paid_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Masukkan 0 jika belum bayar.</small>
                                </div>

                                {{-- Due Amount (Auto Calculate) --}}
                                <div class="mb-3">
                                    <label class="form-label small font-weight-semibold text-dark">
                                        Sisa yang Belum Dibayar
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control bg-light" id="due_display"
                                            value="0" readonly>
                                    </div>
                                    <input type="hidden" name="due_amount" id="due_amount" value="0">
                                </div>

                                {{-- Payment Status Badge --}}
                                <div class="mb-3">
                                    <label class="form-label small font-weight-semibold text-dark">
                                        Status Pembayaran
                                    </label>
                                    <div>
                                        <span class="badge badge-warning" id="payment_status_badge">Belum Lunas</span>
                                    </div>
                                </div>

                                <hr>

                                {{-- Submit Buttons --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submit_btn" disabled>
                                        <i class="cil-save mr-1"></i> Simpan Pembelian
                                    </button>
                                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                                        <i class="cil-x mr-1"></i> Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- row --}}
            </form>
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        #cart_table thead th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 12px 10px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        #cart_table tbody td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('third_party_scripts')
    <script>
        let cart = [];
        let anTotal = null;
        let anDue = null;
        let anPaid = null;

        // Opsi AutoNumeric (gaya Indonesia, tanpa desimal)
        const autoNumericOptions = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0
        };

        if (window.AutoNumeric) {
            anTotal = new AutoNumeric('#total_display', autoNumericOptions);
            anDue = new AutoNumeric('#due_display', autoNumericOptions);
            anPaid = new AutoNumeric('#paid_amount_display', autoNumericOptions);
        }

        // Toggle bank_name field
        document.getElementById('payment_method').addEventListener('change', function() {
            const bankWrapper = document.getElementById('bank_name_wrapper');
            const bankInput = document.getElementById('bank_name');

            if (this.value === 'Transfer') {
                bankWrapper.style.display = 'block';
                bankInput.required = true;
            } else {
                bankWrapper.style.display = 'none';
                bankInput.required = false;
                bankInput.value = '';
            }
        });

        // Add product to cart
        document.getElementById('add_product').addEventListener('click', function() {
            const select = document.getElementById('product_id');
            const selectedOption = select.options[select.selectedIndex];

            if (!select.value) {
                alert('Pilih produk terlebih dahulu');
                return;
            }

            const productId = select.value;
            const productName = selectedOption.dataset.name;
            const productCode = selectedOption.dataset.code;
            const productPrice = parseInt(selectedOption.dataset.price);
            const productStock = parseInt(selectedOption.dataset.stock);

            // Check if already in cart
            const existingIndex = cart.findIndex(item => item.id === productId);
            if (existingIndex !== -1) {
                alert('Produk sudah ada di keranjang');
                return;
            }

            cart.push({
                id: productId,
                name: productName,
                code: productCode,
                price: productPrice,
                stock: productStock,
                qty: 1
            });

            renderCart();
            select.value = '';
        });

        function renderCart() {
            const tbody = document.getElementById('cart_body');
            const emptyRow = document.getElementById('empty_cart');

            if (cart.length === 0) {
                tbody.innerHTML =
                    '<tr id="empty_cart"><td colspan="5" class="text-center text-muted py-4">Belum ada produk dipilih</td></tr>';
                document.getElementById('submit_btn').disabled = true;
            } else {
                tbody.innerHTML = '';
                cart.forEach((item, index) => {
                    const subtotal = item.qty * item.price;
                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <strong>${item.name}</strong><br>
                                <small class="text-muted">${item.code}</small>
                            </td>
                            <td>
                                <input type="number"
                                       class="form-control form-control-sm"
                                       value="${item.qty}" min="1" max="${item.stock}"
                                       onchange="updateQty(${index}, this.value)">
                            </td>
                            <td>
                                <input type="number"
                                       class="form-control form-control-sm"
                                       value="${item.price}"
                                       onchange="updatePrice(${index}, this.value)">
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" value="${formatNumber(subtotal)}" readonly>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="removeItem(${index})">
                                    <i class="cil-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById('submit_btn').disabled = false;
            }

            calculateTotal();
        }

        // Update quantity
        function updateQty(index, qty) {
            qty = parseInt(qty);
            if (qty < 1) qty = 1;
            if (qty > cart[index].stock) {
                alert('Qty melebihi stok tersedia: ' + cart[index].stock);
                qty = cart[index].stock;
            }
            cart[index].qty = qty;
            renderCart();
        }

        // Update price
        function updatePrice(index, price) {
            price = parseInt(price);
            if (price < 0) price = 0;
            cart[index].price = price;
            renderCart();
        }

        // Remove item
        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        // Calculate total
        function calculateTotal() {
            let total = 0;
            cart.forEach(item => {
                total += item.qty * item.price;
            });

            document.getElementById('total_amount').value = total;

            if (anTotal) {
                anTotal.set(total);
            } else {
                document.getElementById('total_display').value = formatNumber(total);
            }

            calculateDue();
        }

        // Hitung sisa & update status bayar
        function calculateDue() {
            const total = parseInt(document.getElementById('total_amount').value) || 0;
            let paid = 0;

            if (anPaid) {
                paid = parseInt(anPaid.getNumber()) || 0;
            } else {
                paid = parseInt(document.getElementById('paid_amount_display').value) || 0;
            }

            const due = total - paid;

            // set hidden field
            document.getElementById('paid_amount').value = paid;
            document.getElementById('due_amount').value = due;

            if (anDue) {
                anDue.set(due < 0 ? 0 : due);
            } else {
                document.getElementById('due_display').value = formatNumber(due);
            }

            const badge = document.getElementById('payment_status_badge');
            if (due === 0 && total > 0) {
                badge.className = 'badge badge-success';
                badge.textContent = 'Lunas';
            } else {
                badge.className = 'badge badge-warning';
                badge.textContent = 'Belum Lunas';
            }
        }

        // Trigger hitung saat input bayar berubah
        document.getElementById('paid_amount_display').addEventListener('input', function() {
            calculateDue();
        });

        // Format number with thousand separator (fallback tampilan)
        function formatNumber(num) {
            num = parseInt(num) || 0;
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Form submission - minimal 1 produk
        document.querySelector('form').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 produk ke keranjang.');
                return false;
            }

            return true;
        });

        // Inisialisasi awal
        calculateTotal();
    </script>
@endsection
