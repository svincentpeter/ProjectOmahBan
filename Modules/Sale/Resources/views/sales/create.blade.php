@extends('layouts.app-flowbite')

@section('title', 'Buat Penjualan Baru')

@section('content')
    <div class="px-4 pt-4 mb-4">
        {{-- Breadcrumb --}}
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <i class="bi bi-house-door-fill mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <a href="{{ route('sales.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Penjualan</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Buat Baru</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Search Product Section (Full Width) --}}
        <div class="mb-6">
            <livewire:search-product />
        </div>

        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @include('utils.alerts')
            
            <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                @csrf

                {{-- Reference & Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="reference" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-upc-scan text-gray-500"></i>
                            </div>
                            <input type="text" name="reference" id="reference" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 cursor-not-allowed" value="SL" readonly required>
                        </div>
                    </div>
                    <div>
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500"></i>
                            </div>
                            <input type="date" name="date" id="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Cart Component --}}
                <div class="mb-8">
                    <livewire:product-cart :cartInstance="'sale'" />
                </div>

                {{-- Status, Payment, Amount --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="Pending">Pending</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_method" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment Method <span class="text-red-500">*</span></label>
                        <select id="payment_method" name="payment_method" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="paid_amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Amount Received <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-gray-500 font-bold">{{ settings()->currency->symbol }}</span>
                                </div>
                                <input type="text" id="paid_amount" name="paid_amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-none rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 p-2.5" required>
                            </div>
                            <button type="button" id="getTotalAmount" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-700 border border-blue-700 rounded-r-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors" data-total="{{ \Cart::instance('sale')->total() }}">
                                <i class="bi bi-check2-square text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="mb-6">
                    <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Note (If Needed)</label>
                    <textarea id="note" name="note" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Add any extra notes here..."></textarea>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center shadow-lg hover:shadow-xl transition-all">
                        Create Sale <i class="bi bi-check-lg ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Inisialisasi maskMoney
            $('#paid_amount').maskMoney({
                prefix: '{{ settings()->currency->symbol }}',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                allowZero: true,
            });

            // Tombol "Get Total"
            $('#getTotalAmount').on('click', function () {
                const total = $(this).data('total');
                $('#paid_amount').maskMoney('mask', total);
            });

            // Unmask sebelum submit
            $('#sale-form').on('submit', function () {
                const paid = $('#paid_amount').maskMoney('unmasked')[0];
                $('#paid_amount').val(paid);
            });
        });
    </script>
@endpush
