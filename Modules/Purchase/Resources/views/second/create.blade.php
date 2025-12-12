@extends('layouts.app-flowbite')

@section('title', 'Input Pembelian Stok Bekas')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pembelian Bekas', 'url' => route('purchases.second.index')],
            ['text' => 'Input Pembelian', 'url' => '#'],
        ],
    ])

    <form action="{{ route('purchases.second.store') }}" method="POST" id="purchase-form">
        @csrf
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
                            <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>

                        {{-- Reference --}}
                        <div>
                            <label for="reference"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference</label>
                            <input type="text" name="reference" id="reference" value="PB-{{ date('YmdHis') }}" readonly
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                        </div>

                        {{-- Customer Name --}}
                        <div>
                            <label for="customer_name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Customer <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Nama Penjual (Customer)">
                        </div>

                        {{-- Customer Phone --}}
                        <div>
                            <label for="customer_phone"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. HP Customer</label>
                            <input type="text" name="customer_phone" id="customer_phone"
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
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Jika Completed, stok produk akan bertambah.</p>
                        </div>

                        {{-- Note --}}
                        <div>
                            <label for="note"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                            <textarea name="note" id="note" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"></textarea>
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
                                <option value="Cash">Cash</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Other">Lainnya</option>
                            </select>
                        </div>

                        {{-- Total Amount --}}
                        <div>
                            <label for="total_amount"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total
                                Pembelian</label>
                            <input type="text" id="total_amount_display" readonly
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 text-right font-bold cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                                value="Rp 0">
                            <input type="hidden" name="total_amount" id="total_amount" value="0">
                        </div>

                        {{-- Paid Amount --}}
                        <div>
                            <label for="paid_amount"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Bayar <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="paid_amount" id="paid_amount" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 text-right mask-money dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>

                        {{-- Due Amount --}}
                        <div>
                            <label for="due_amount"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sisa Tagihan</label>
                            <input type="text" id="due_amount_display" readonly
                                class="bg-gray-100 border border-gray-300 text-red-600 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 text-right font-bold cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-red-400"
                                value="Rp 0">
                            <input type="hidden" name="due_amount" id="due_amount" value="0">
                        </div>

                        <button type="submit"
                            class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-all">
                            Simpan Pembelian
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

            // Cart Logic
            let cart = [];

            // Format Currency
            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            // Unformat Currency (remove Rp and dots)
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
