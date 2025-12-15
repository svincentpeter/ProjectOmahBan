@extends('layouts.app-flowbite')

@section('title', 'Tambah Pembelian Stok')

@section('content')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pembelian Stok', 'url' => route('purchases.index')],
            ['text' => 'Tambah', 'url' => '#'],
        ],
    ])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Product Search & Cart --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Product Search Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-lg">
                    <h5 class="text-lg font-bold text-gray-800 dark:text-white mb-1">
                        <i class="cil-search mr-2 text-purple-600"></i> Pilih Produk
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Cari dan tambahkan produk ke daftar pembelian.
                    </p>
                </div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-grow">
                            <label for="product_id" class="sr-only">Pilih Produk</label>
                            <select id="product_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                <option value="">-- Pilih Produk --</option>
                                {{-- NOTE: Optimasi query jika produk ribuan --}}
                                @foreach (\Modules\Product\Entities\Product::all() as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->product_name }}"
                                        data-code="{{ $product->product_code }}" data-price="{{ $product->product_cost }}"
                                        data-stock="{{ $product->product_quantity }}">
                                        {{ $product->product_name }} ({{ $product->product_code }}) - Stok:
                                        {{ $product->product_quantity }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="add_product"
                            class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-colors whitespace-nowrap">
                            <i class="cil-plus mr-1"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>

            {{-- Cart Items Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-lg">
                    <h5 class="text-lg font-bold text-gray-800 dark:text-white mb-1">
                        <i class="cil-cart mr-2 text-purple-600"></i> Item Pembelian
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Daftar produk yang akan dibeli.
                    </p>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Harga Beli</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart_body" class="divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Cart Items Rendered Here --}}
                            <tr id="empty_cart">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="cil-cart text-2xl mb-2 block text-gray-300"></i>
                                    Belum ada produk dipilih
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Purchase Details Form --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-lg">
                    <h5 class="text-lg font-bold text-gray-800 dark:text-white mb-1">
                        <i class="cil-paper-plane mr-2 text-purple-600"></i> Detail Pembelian
                    </h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Isi informasi supplier dan pembayaran.
                    </p>
                </div>
                <div class="p-6">
                    <form action="{{ route('purchases.store') }}" method="POST" id="purchase_form">
                        @csrf
                        {{-- Hidden Input for Cart JSON --}}
                        <input type="hidden" name="cart_json" id="cart_json">

                        <div class="grid grid-cols-1 gap-4">
                            {{-- Supplier --}}
                            <div>
                                <label for="supplier_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <select id="supplier_id" name="supplier_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach (\Modules\People\Entities\Supplier::all() as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div>
                                <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Tanggal Pembelian <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                            </div>

                            {{-- Payment Method --}}
                            <div>
                                <label for="payment_method"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Metode Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <select id="payment_method" name="payment_method"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer">Transfer</option>
                                    <option value="Credit">Credit/Hutang</option>
                                    <option value="Other">Lainnya</option>
                                </select>
                            </div>

                            {{-- Bank Name --}}
                            <div class="hidden" id="bank_name_div">
                                <label for="bank_name"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Nama Bank
                                </label>
                                <select id="bank_name" name="bank_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option value="">-- Pilih Bank --</option>
                                    <option value="BCA">BCA</option>
                                    <option value="BRI">BRI</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BNI">BNI</option>
                                </select>
                            </div>

                            {{-- Reference --}}
                            <div>
                                <label for="reference" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Reference (Opsional)
                                </label>
                                <input type="text" name="reference" id="reference"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    value="PB-{{ date('Ymd') }}-AUTO" readonly>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Status
                                </label>
                                <select id="status" name="status"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option value="Completed">Completed</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Ordered">Ordered</option>
                                </select>
                            </div>

                            {{-- Note --}}
                            <div>
                                <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Catatan
                                </label>
                                <textarea name="note" id="note" rows="3"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"></textarea>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 my-2">

                            {{-- Total Calculation --}}
                            <div class="space-y-3">
                                <div class="flex justify-between items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                    <span>Total Belanja:</span>
                                    <span id="grand_total" class="text-gray-800 dark:text-white font-bold text-lg">
                                        Rp 0
                                    </span>
                                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                                </div>
                                {{-- Quick Payment Buttons --}}
                                <div class="flex gap-2 mb-3">
                                    <button type="button" onclick="setPaymentPercentage(100)" 
                                        class="flex-1 px-3 py-2 text-xs font-medium text-center text-green-600 border border-green-600 rounded-lg hover:bg-green-50 focus:ring-4 focus:ring-green-300 dark:text-green-500 dark:border-green-500 dark:hover:bg-gray-800 dark:focus:ring-green-800 transition-colors">
                                        💯 Lunas (100%)
                                    </button>
                                    <button type="button" onclick="setPaymentPercentage(50)" 
                                        class="flex-1 px-3 py-2 text-xs font-medium text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 focus:ring-4 focus:ring-blue-300 dark:text-blue-500 dark:border-blue-500 dark:hover:bg-gray-800 dark:focus:ring-blue-800 transition-colors">
                                        50%
                                    </button>
                                    <button type="button" onclick="setPaymentPercentage(25)" 
                                        class="flex-1 px-3 py-2 text-xs font-medium text-center text-orange-600 border border-orange-600 rounded-lg hover:bg-orange-50 focus:ring-4 focus:ring-orange-300 dark:text-orange-500 dark:border-orange-500 dark:hover:bg-gray-800 dark:focus:ring-orange-800 transition-colors">
                                        25%
                                    </button>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label for="paid_amount" class="text-sm font-medium text-gray-900 dark:text-white mr-2">
                                        Jumlah Bayar:
                                    </label>
                                    <input type="text" name="paid_amount" id="paid_amount" required value="0"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-1/2 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500 text-right">
                                </div>
                                <div class="flex justify-between items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                    <span>Sisa Tagihan:</span>
                                    <span id="due_amount_display" class="text-red-500 font-bold">
                                        Rp 0
                                    </span>
                                    <input type="hidden" name="due_amount" id="due_amount" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" id="btn_submit"
                                class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-3 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="cil-save mr-2"></i> Simpan Pembelian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function() {
            let cart = [];

            // Money Mask
            $('#paid_amount').maskMoney({
                prefix: 'Rp ',
                thousands: '.',
                decimal: ',',
                allowZero: true,
                precision: 0
            });

            // Handle Bank Switch
            $('#payment_method').change(function() {
                if ($(this).val() == 'Transfer') {
                    $('#bank_name_div').removeClass('hidden');
                } else {
                    $('#bank_name_div').addClass('hidden');
                }
            });

            // Add Product Logic
            $('#add_product').click(function() {
                const select = $('#product_id');
                const selected = select.find(':selected');
                const id = select.val();
                
                if (!id) {
                    Swal.fire('Error', 'Silakan pilih produk terlebih dahulu', 'error');
                    return;
                }

                // Check existing
                const exists = cart.find(item => item.id == id);
                if (exists) {
                    Swal.fire('Info', 'Produk sudah ada di keranjang', 'info');
                    return;
                }

                cart.push({
                    id: id,
                    name: selected.data('name'),
                    code: selected.data('code'),
                    price: parseFloat(selected.data('price')) || 0,
                    stock: parseInt(selected.data('stock')) || 0,
                    qty: 1
                });

                select.val(''); // Reset select
                renderCart();
            });

            // Render Cart
            window.renderCart = function() {
                const tbody = $('#cart_body');
                tbody.empty();

                if (cart.length === 0) {
                    tbody.html(`
                        <tr id="empty_cart">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="cil-cart text-2xl mb-2 block text-gray-300"></i>
                                Belum ada produk dipilih
                            </td>
                        </tr>
                    `);
                    $('#btn_submit').prop('disabled', true);
                } else {
                    $('#btn_submit').prop('disabled', false);
                    cart.forEach((item, index) => {
                        const subtotal = item.qty * item.price;
                        tbody.append(`
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    ${item.name} <br>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ${item.code}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" min="1"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-20 p-1 mx-auto dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        value="${item.qty}"
                                        onchange="updateQty(${index}, this.value)">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <input type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-1 text-right dark:bg-gray-700 dark:border-gray-600 dark:text-white price-input"
                                        value="${formatCurrency(item.price)}"
                                        data-index="${index}">
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                    ${formatCurrency(subtotal)}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="removeItem(${index})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400">
                                        <i class="cil-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    // Init mask for new inputs
                    $('.price-input').maskMoney({
                        prefix: 'Rp ',
                        thousands: '.',
                        decimal: ',',
                        allowZero: true,
                        precision: 0
                    });

                    $('.price-input').on('change', function() {
                        const index = $(this).data('index');
                        const val = $(this).maskMoney('unmasked')[0];
                        updatePrice(index, val);
                    });
                }
                calculateSummary();
            };

            // Helpers
            window.updateQty = function(index, qty) {
                qty = parseInt(qty);
                if (qty < 1) qty = 1;
                cart[index].qty = qty;
                renderCart();
            };

            window.updatePrice = function(index, price) {
                cart[index].price = parseFloat(price);
                renderCart();
            };

            window.removeItem = function(index) {
                cart.splice(index, 1);
                renderCart();
            };

            window.calculateSummary = function() {
                let total = 0;
                cart.forEach(item => total += (item.qty * item.price));
                
                $('#total_amount').val(total);
                $('#grand_total').text(formatCurrency(total));

                // Manual parsing for robustness
                let paidVal = $('#paid_amount').val() || '';
                // Remove non-digit characters except comma (for decimal, though we use 0 precision usually)
                // Assuming format: Rp 1.000.000
                let cleanStr = paidVal.replace(/[^\d]/g, ''); 
                let paid = parseFloat(cleanStr) || 0;
                
                let due = Math.max(0, total - paid);
                
                $('#due_amount').val(due);
                $('#due_amount_display').text(formatCurrency(due));
            };

            // Quick Payment Percentage Function
            window.setPaymentPercentage = function(percentage) {
                let total = parseFloat($('#total_amount').val()) || 0;
                let amount = Math.round(total * (percentage / 100));
                
                $('#paid_amount').val(amount);
                $('#paid_amount').maskMoney('mask');
                
                calculateSummary();
            };

            window.formatCurrency = function(num) {
                return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            };

            $('#paid_amount').on('keyup change', function() {
                calculateSummary();
            });

            // Form Submit
            $('#purchase_form').on('submit', function(e) {
                if(cart.length === 0) {
                    e.preventDefault();
                    Swal.fire('Error', 'Keranjang masih kosong!', 'error');
                    return false;
                }
                
                // Inject Cart to Hidden Input
                $('#cart_json').val(JSON.stringify(cart));

                // Unmask paid_amount before submit to prevent validation error
                // Backend already handles this with prepareForValidation, but this is double safety
                let paidVal = $('#paid_amount').val();
                let cleanPaid = paidVal.replace(/[^\d]/g, '');
                $('#paid_amount').val(cleanPaid);

                return true;
            });

        });
    </script>
@endpush
