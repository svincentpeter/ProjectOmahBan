@extends('layouts.app-flowbite')

@section('title', 'Edit Pembelian Stok Bekas')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pembelian Bekas', 'url' => route('purchases.second.index')],
            ['text' => 'Edit Pembelian', 'url' => '#'],
        ],
    ])

    <form action="{{ route('purchases.second.update', $purchaseSecond) }}" method="POST" id="purchase-form">
        @csrf
        @method('patch')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Product Selection & Cart --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Product Selection Card --}}
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-cart-plus mr-2 text-purple-600"></i>
                            Pilih Produk Bekas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                            <div class="md:col-span-4">
                                <label for="product_id"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Produk Bekas <span
                                        class="text-red-500">*</span></label>
                                <select id="product_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    <option value="">-- Pilih Produk Bekas --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-code="{{ $product->uniquecode }}" data-price="{{ $product->purchaseprice }}"
                                            data-condition="{{ $product->conditionnotes }}">
                                            {{ $product->name }} ({{ $product->uniquecode }}) -
                                            {{ rupiah($product->purchaseprice) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih produk ban/velg bekas yang
                                    akan dibeli</p>
                            </div>
                            <div class="md:col-span-2">
                                <button type="button" id="add-to-cart"
                                    class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800">
                                    <i class="bi bi-plus-lg mr-1"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cart Table --}}
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-cart4 mr-2 text-purple-600"></i>
                            Keranjang Pembelian
                        </h3>
                        <button type="button" id="clear-cart"
                            class="text-red-600 hover:text-white border border-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 transition-all">
                            <i class="bi bi-trash mr-1"></i> Kosongkan
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">#</th>
                                    <th scope="col" class="px-6 py-3">Produk</th>
                                    <th scope="col" class="px-6 py-3">Kode</th>
                                    <th scope="col" class="px-6 py-3">Kondisi</th>
                                    <th scope="col" class="px-6 py-3 text-right">Harga Beli</th>
                                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body">
                                <tr id="empty-cart-row">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="bi bi-cart-x text-4xl mb-3 text-gray-300"></i>
                                            <p>Keranjang kosong. Tambahkan produk terlebih dahulu.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Column: Information --}}
            <div class="space-y-6">
                {{-- Detail Pembelian --}}
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Detail Pembelian
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Date --}}
                        <div>
                            <label for="date"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="date" id="date" value="{{ $purchaseSecond->date->format('Y-m-d') }}" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>

                        {{-- Reference --}}
                        <div>
                            <label for="reference"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference</label>
                            <input type="text" name="reference" id="reference" value="{{ $purchaseSecond->reference }}" readonly
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                        </div>

                        {{-- Customer Name --}}
                        <div>
                            <label for="customer_name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Customer <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ $purchaseSecond->customer_name }}" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Nama Penjual (Customer)">
                        </div>

                        {{-- Customer Phone --}}
                        <div>
                            <label for="customer_phone"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. HP Customer</label>
                            <input type="text" name="customer_phone" id="customer_phone" value="{{ $purchaseSecond->customer_phone }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="Pending" {{ $purchaseSecond->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Completed" {{ $purchaseSecond->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Jika Completed, stok produk akan bertambah.</p>
                        </div>

                        {{-- Note --}}
                        <div>
                            <label for="note"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                            <textarea name="note" id="note" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">{{ $purchaseSecond->note }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Pembayaran
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Payment Method --}}
                        <div>
                            <label for="payment_method"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Metode Bayar <span
                                    class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="Cash" {{ $purchaseSecond->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Transfer" {{ $purchaseSecond->payment_method == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="Other" {{ $purchaseSecond->payment_method == 'Other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        {{-- Total Amount --}}
                        <div>
                            <label for="total_amount"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total
                                Pembelian</label>
                            <input type="text" id="total_amount_display" readonly
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 text-right font-bold cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                                value="{{ rupiah($purchaseSecond->total_amount) }}">
                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $purchaseSecond->total_amount }}">
                        </div>

                        {{-- Paid Amount --}}
                        <div>
                            <label for="paid_amount"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Bayar <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="paid_amount" id="paid_amount" value="{{ $purchaseSecond->paid_amount }}" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 text-right mask-money dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>

                        {{-- Due Amount --}}
                        <div>
                            <label for="due_amount"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sisa Tagihan</label>
                            <input type="text" id="due_amount_display" readonly
                                class="bg-gray-100 border border-gray-300 text-red-600 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 text-right font-bold cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-red-400"
                                value="{{ rupiah($purchaseSecond->due_amount) }}">
                            <input type="hidden" name="due_amount" id="due_amount" value="{{ $purchaseSecond->due_amount }}">
                        </div>

                        <button type="submit"
                            class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-all">
                            Update Pembelian
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Hidden Input for Cart JSON --}}
        <input type="hidden" name="cart_json" id="cart_json" value="[]">
    </form>
@endsection

@push('page_scripts')
    {{-- jQuery Mask Money --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script>
        $(document).ready(function() {
            // Init Mask Money
            $('.mask-money').maskMoney({
                prefix: 'Rp ',
                thousands: '.',
                decimal: ',',
                precision: 0,
                allowZero: true
            });
            // Trigget mask on load
            $('#paid_amount').maskMoney('mask');

            // Cart Logic
            let cart = [];

            // Initialize Cart from Server Data
            @foreach ($purchaseSecond->purchaseSecondDetails as $detail)
                cart.push({
                    id: "{{ $detail->product_second_id }}",
                    name: "{{ $detail->product_name }}",
                    code: "{{ $detail->product_code }}",
                    condition: "{{ $detail->condition_notes }}",
                    price: parseFloat("{{ $detail->unit_price }}")
                });
            @endforeach

            updateCartTable();

            // Format Currency
            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            // Unformat Currency
            function unformatCurrency(str) {
                return parseFloat(str.replace(/[^0-9,-]+/g, "").replace(',', '.'));
            }

            // Add to Cart
            $('#add-to-cart').click(function() {
                const productId = $('#product_id').val();
                const selectedOption = $('#product_id option:selected');
                
                if (!productId) {
                    Swal.fire('Error', 'Silakan pilih produk terlebih dahulu.', 'error');
                    return;
                }

                // Check if already in cart
                const exists = cart.find(item => item.id == productId);
                if (exists) {
                    Swal.fire('Error', 'Produk ini sudah ada di keranjang.', 'warning');
                    return;
                }

                const item = {
                    id: productId,
                    name: selectedOption.data('name'),
                    code: selectedOption.data('code'),
                    condition: selectedOption.data('condition'),
                    price: parseFloat(selectedOption.data('price'))
                };

                cart.push(item);
                updateCartTable();
                
                // Reset Selection
                $('#product_id').val('').trigger('change');
            });

            // Remove from Cart
            $(document).on('click', '.remove-item', function() {
                const index = $(this).data('index');
                cart.splice(index, 1);
                updateCartTable();
            });

            // Clear Cart
            $('#clear-cart').click(function() {
                cart = [];
                updateCartTable();
            });

            // Update Cart UI
            function updateCartTable() {
                const tbody = $('#cart-table-body');
                tbody.empty();

                if (cart.length === 0) {
                    tbody.html(`
                        <tr id="empty-cart-row">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-cart-x text-4xl mb-3 text-gray-300"></i>
                                    <p>Keranjang kosong. Tambahkan produk terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    $('#total_amount').val(0);
                    $('#total_amount_display').val(formatCurrency(0));
                } else {
                    let total = 0;
                    cart.forEach((item, index) => {
                        total += item.price;
                        tbody.append(`
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">${index + 1}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">${item.name}</td>
                                <td class="px-6 py-4">${item.code}</td>
                                <td class="px-6 py-4">${item.condition}</td>
                                <td class="px-6 py-4 text-right">${formatCurrency(item.price)}</td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-900" data-index="${index}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    $('#total_amount').val(total);
                    $('#total_amount_display').val(formatCurrency(total));
                }

                calculateDueAmount();
                updateCartJsonInput();
            }

            // Calculate Due Amount
            function calculateDueAmount() {
                const total = parseFloat($('#total_amount').val()) || 0;
                let paid = $('#paid_amount').maskMoney('unmasked')[0] || 0;
                let due = total - paid;
                
                // Ensure non-negative due for display, but logic allows overpayment (change)
                if (due < 0) due = 0; 
                
                $('#due_amount').val(due);
                $('#due_amount_display').val(formatCurrency(due));
            }

            // Update Hidden Input
            function updateCartJsonInput() {
                $('#cart_json').val(JSON.stringify(cart));
            }

            // Listeners for calculation
            $('#paid_amount').on('keyup change', calculateDueAmount);

            // Form Submit Validation
            $('#purchase-form').submit(function(e) {
                if (cart.length === 0) {
                    e.preventDefault();
                    Swal.fire('Error', 'Keranjang pembelian tidak boleh kosong!', 'error');
                }
            });
        });
    </script>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.second.index') }}">Pembelian Bekas</a></li>
        <li class="breadcrumb-item active">Edit Pembelian</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('purchases.second.update', $purchaseSecond) }}" method="POST" id="purchase-form">
            @csrf
            @method('PATCH')

            {{-- Warning Alert if Completed --}}
            @if ($purchaseSecond->status === 'Completed')
                <div class="alert alert-warning mb-4">
                    <i class="cil-warning mr-2"></i>
                    <strong>Perhatian:</strong> Pembelian dengan status <strong>Completed</strong> tidak dapat diedit.
                    <a href="{{ route('purchases.second.index') }}" class="alert-link">Kembali ke daftar</a>
                </div>
            @endif

            <div class="row">
                {{-- LEFT: Product Selection & Cart --}}
                <div class="col-lg-8 mb-4">
                    {{-- Product Selection Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="cil-basket mr-2"></i>
                                Pilih Produk Bekas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10 mb-3">
                                    <label for="product_id" class="form-label font-weight-semibold">
                                        Produk Bekas <span class="text-danger">*</span>
                                    </label>
                                    <select id="product_id" class="form-control">
                                        <option value="">-- Pilih Produk Bekas --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-code="{{ $product->uniquecode }}"
                                                data-price="{{ $product->purchaseprice }}"
                                                data-condition="{{ $product->conditionnotes }}">
                                                {{ $product->name }} ({{ $product->uniquecode }}) -
                                                {{ rupiah($product->purchaseprice) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Tambahkan atau ubah produk di keranjang</small>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label font-weight-semibold">&nbsp;</label>
                                    <button type="button" id="add-to-cart" class="btn btn-primary btn-block">
                                        <i class="cil-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cart Card --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 font-weight-bold text-dark">
                                    <i class="cil-cart mr-2 text-primary"></i>
                                    Keranjang Pembelian
                                </h6>
                                <button type="button" id="clear-cart" class="btn btn-sm btn-outline-danger">
                                    <i class="cil-trash"></i> Kosongkan
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Produk</th>
                                            <th>Kode</th>
                                            <th>Kondisi</th>
                                            <th width="20%">Harga Beli</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-table-body">
                                        <tr id="empty-cart-row">
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="cil-info" style="font-size: 2rem;"></i>
                                                <p class="mb-0 mt-2">Keranjang kosong. Tambahkan produk terlebih dahulu.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Purchase Details & Summary --}}
                <div class="col-lg-4 mb-4">
                    {{-- Purchase Information --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="cil-notes mr-2 text-primary"></i>
                                Informasi Pembelian
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Tanggal --}}
                            <div class="form-group">
                                <label for="date" class="form-label font-weight-semibold">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date" id="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date', $purchaseSecond->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Reference --}}
                            <div class="form-group">
                                <label for="reference" class="form-label font-weight-semibold">
                                    Reference <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="reference" id="reference"
                                    class="form-control bg-light @error('reference') is-invalid @enderror"
                                    value="{{ old('reference', $purchaseSecond->reference) }}" readonly>
                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Reference tidak dapat diubah</small>
                            </div>

                            {{-- Customer Name --}}
                            <div class="form-group">
                                <label for="customer_name" class="form-label font-weight-semibold">
                                    Nama Customer <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="customer_name" id="customer_name"
                                    class="form-control @error('customer_name') is-invalid @enderror"
                                    value="{{ old('customer_name', $purchaseSecond->customer_name) }}"
                                    placeholder="Nama customer yang jual produk bekas" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Customer Phone --}}
                            <div class="form-group">
                                <label for="customer_phone" class="form-label font-weight-semibold">
                                    Nomor HP (Opsional)
                                </label>
                                <input type="text" name="customer_phone" id="customer_phone"
                                    class="form-control @error('customer_phone') is-invalid @enderror"
                                    value="{{ old('customer_phone', $purchaseSecond->customer_phone) }}"
                                    placeholder="08xxxxxxxxxx">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="form-group">
                                <label for="status" class="form-label font-weight-semibold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="Pending"
                                        {{ old('status', $purchaseSecond->status) == 'Pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="Completed"
                                        {{ old('status', $purchaseSecond->status) == 'Completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="cil-info mr-1"></i>
                                    Pilih <strong>Completed</strong> jika produk sudah masuk ke stok
                                </small>
                            </div>

                            {{-- Payment Method --}}
                            <div class="form-group">
                                <label for="payment_method" class="form-label font-weight-semibold">
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="payment_method"
                                    class="form-control @error('payment_method') is-invalid @enderror" required>
                                    <option value="Tunai"
                                        {{ old('payment_method', $purchaseSecond->payment_method) == 'Tunai' ? 'selected' : '' }}>
                                        Tunai
                                    </option>
                                    <option value="Transfer"
                                        {{ old('payment_method', $purchaseSecond->payment_method) == 'Transfer' ? 'selected' : '' }}>
                                        Transfer Bank
                                    </option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Bank Name (conditional) --}}
                            <div class="form-group" id="bank-name-group" style="display: none;">
                                <label for="bank_name" class="form-label font-weight-semibold">
                                    Nama Bank <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="bank_name" id="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror"
                                    value="{{ old('bank_name', $purchaseSecond->bank_name) }}"
                                    placeholder="Contoh: BCA, Mandiri, BRI">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Note --}}
                            <div class="form-group">
                                <label for="note" class="form-label font-weight-semibold">
                                    Catatan (Opsional)
                                </label>
                                <textarea name="note" id="note" rows="3" class="form-control @error('note') is-invalid @enderror"
                                    placeholder="Tambahkan catatan jika diperlukan...">{{ old('note', $purchaseSecond->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Payment Summary --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="cil-calculator mr-2"></i>
                                Ringkasan Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Total Amount --}}
                            <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                <span class="font-weight-semibold">Total Pembelian:</span>
                                <span class="font-weight-bold text-primary" id="display-total">Rp 0</span>
                            </div>

                            {{-- Paid Amount --}}
                            <div class="form-group">
                                <label for="paid_amount" class="form-label font-weight-semibold">
                                    Jumlah Bayar <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="paid_amount" id="paid_amount"
                                    class="form-control @error('paid_amount') is-invalid @enderror"
                                    value="{{ old('paid_amount', $purchaseSecond->paid_amount) }}" min="0"
                                    step="1">
                                @error('paid_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Due Amount --}}
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-semibold">Sisa Hutang:</span>
                                <span class="font-weight-bold text-danger" id="display-due">Rp 0</span>
                            </div>

                            {{-- Payment Status (auto) --}}
                            <div class="alert mb-0" id="payment-status-alert">
                                <strong>Status:</strong>
                                <span id="payment-status-text">Belum Lunas</span>
                            </div>

                            {{-- Hidden Inputs --}}
                            <input type="hidden" name="total_amount" id="total_amount" value="0">
                            <input type="hidden" name="due_amount" id="due_amount" value="0">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success btn-block btn-lg">
                            <i class="cil-save mr-2"></i>
                            Update Pembelian
                        </button>
                        <a href="{{ route('purchases.second.index') }}" class="btn btn-secondary btn-block mt-2">
                            <i class="cil-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('page_styles')
    <style>
        /* ========== Card Shadows ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Cart Table Styling ========== */
        .table thead th {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table tbody tr:hover {
            background-color: rgba(72, 52, 223, 0.03);
        }

        /* ========== Form Controls ========== */
        .form-label {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #2d3748;
        }

        .form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.1);
        }

        /* ========== Payment Status Alert ========== */
        #payment-status-alert {
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        #payment-status-alert.alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        #payment-status-alert.alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        /* ========== Action Button in Cart ========== */
        .btn-remove-item {
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 4px;
        }

        /* ========== Warning Alert ========== */
        .alert-warning {
            border-left: 4px solid #f9b115;
        }

        /* ========== Responsive ========== */
        @media (max-width: 992px) {

            .col-lg-8,
            .col-lg-4 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize cart from session (loaded by controller)
            let cart = @json(session('cart_purchase_second', []));

            // ========== Add to Cart ==========
            $('#add-to-cart').click(function() {
                const productId = $('#product_id').val();
                const productName = $('#product_id option:selected').data('name');
                const productCode = $('#product_id option:selected').data('code');
                const productPrice = parseInt($('#product_id option:selected').data('price'));
                const productCondition = $('#product_id option:selected').data('condition');

                // Validation
                if (!productId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Pilih produk terlebih dahulu!',
                        confirmButtonColor: '#4834DF'
                    });
                    return;
                }

                // Check if product already in cart
                const exists = cart.find(item => item.id == productId);
                if (exists) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Produk Sudah Ada',
                        text: 'Produk ini sudah ada di keranjang!',
                        confirmButtonColor: '#4834DF'
                    });
                    return;
                }

                // Add to cart
                cart.push({
                    id: productId,
                    name: productName,
                    code: productCode,
                    price: productPrice,
                    condition: productCondition || '-'
                });

                // Reset select
                $('#product_id').val('');

                // Re-render cart
                renderCart();

                // Show success toast
                toastr.success('Produk ditambahkan ke keranjang!', 'Berhasil');
            });

            // ========== Remove from Cart ==========
            $(document).on('click', '.btn-remove-item', function() {
                const index = $(this).data('index');

                Swal.fire({
                    icon: 'question',
                    title: 'Hapus Produk?',
                    text: 'Produk akan dihapus dari keranjang',
                    showCancelButton: true,
                    confirmButtonColor: '#e55353',
                    cancelButtonColor: '#768192',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart.splice(index, 1);
                        renderCart();
                        toastr.info('Produk dihapus dari keranjang', 'Info');
                    }
                });
            });

            // ========== Clear Cart ==========
            $('#clear-cart').click(function() {
                if (cart.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Keranjang Kosong',
                        text: 'Tidak ada produk di keranjang',
                        confirmButtonColor: '#4834DF'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Kosongkan Keranjang?',
                    text: 'Semua produk akan dihapus dari keranjang',
                    showCancelButton: true,
                    confirmButtonColor: '#e55353',
                    cancelButtonColor: '#768192',
                    confirmButtonText: 'Ya, Kosongkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart = [];
                        renderCart();
                        toastr.success('Keranjang berhasil dikosongkan', 'Berhasil');
                    }
                });
            });

            // ========== Render Cart Table ==========
            function renderCart() {
                const tbody = $('#cart-table-body');
                tbody.empty();

                if (cart.length === 0) {
                    tbody.html(`
                        <tr id="empty-cart-row">
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="cil-info" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">Keranjang kosong. Tambahkan produk terlebih dahulu.</p>
                            </td>
                        </tr>
                    `);
                } else {
                    cart.forEach((item, index) => {
                        tbody.append(`
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>
                                    <strong>${item.name}</strong>
                                </td>
                                <td><code>${item.code}</code></td>
                                <td>
                                    <small class="text-muted">${item.condition}</small>
                                </td>
                                <td>
                                    <strong class="text-primary">${formatRupiah(item.price)}</strong>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-item" data-index="${index}">
                                        <i class="cil-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }

                // Update summary
                updateSummary();
            }

            // ========== Update Payment Summary ==========
            function updateSummary() {
                const total = cart.reduce((sum, item) => sum + item.price, 0);
                const paid = parseInt($('#paid_amount').val()) || 0;
                const due = Math.max(0, total - paid);

                $('#display-total').text(formatRupiah(total));
                $('#display-due').text(formatRupiah(due));

                $('#total_amount').val(total);
                $('#due_amount').val(due);

                // Update payment status
                const statusAlert = $('#payment-status-alert');
                const statusText = $('#payment-status-text');

                if (due === 0 && total > 0) {
                    statusAlert.removeClass('alert-warning').addClass('alert-success');
                    statusText.text('Lunas');
                } else {
                    statusAlert.removeClass('alert-success').addClass('alert-warning');
                    statusText.text('Belum Lunas');
                }
            }

            // ========== Watch Paid Amount Changes ==========
            $('#paid_amount').on('input', function() {
                updateSummary();
            });

            // ========== Payment Method Change ==========
            $('#payment_method').change(function() {
                const method = $(this).val();
                if (method === 'Transfer') {
                    $('#bank-name-group').show();
                    $('#bank_name').attr('required', true);
                } else {
                    $('#bank-name-group').hide();
                    $('#bank_name').attr('required', false);
                }
            });

            // Trigger on page load
            $('#payment_method').trigger('change');

            // ========== Format Rupiah Helper ==========
            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // ========== Form Submit Validation ==========
            $('#purchase-form').submit(function(e) {
                if (cart.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Keranjang Kosong',
                        text: 'Tambahkan minimal 1 produk ke keranjang!',
                        confirmButtonColor: '#e55353'
                    });
                    return false;
                }

                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Store cart to session via AJAX before submit
                $.ajax({
                    url: '{{ route('purchases.second.update', $purchaseSecond) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PATCH',
                        cart: JSON.stringify(cart)
                    },
                    async: false,
                    success: function(response) {
                        // Continue form submit
                    },
                    error: function(xhr) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan data',
                            confirmButtonColor: '#e55353'
                        });
                    }
                });
            });

            // ========== Initialize: Render cart from session ==========
            renderCart();

            // ========== Show info if cart loaded from database ==========
            @if (session()->has('cart_purchase_second'))
                toastr.info('Cart berhasil dimuat dari pembelian sebelumnya', 'Info', {
                    timeOut: 3000
                });
            @endif
        });
    </script>
@endpush
