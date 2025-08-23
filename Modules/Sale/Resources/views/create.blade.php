@extends('layouts.app')

@section('title', 'Create Sale')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('utils.alerts')
                        <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">Reference <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="reference"
                                            required
                                            readonly
                                            value="SL"
                                        >
                                    </div>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            name="date"
                                            required
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                        >
                                    </div>
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'sale'"/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select
                                            class="form-control"
                                            name="status"
                                            id="status"
                                            required
                                        >
                                            <option value="Pending">Pending</option>
                                            <option value="Shipped">Shipped</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                        <select
                                            class="form-control"
                                            name="payment_method"
                                            id="payment_method"
                                            required
                                        >
                                            <option value="Cash">Cash</option>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="paid_amount">Amount Received <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input
                                                id="paid_amount"
                                                type="text"
                                                class="form-control"
                                                name="paid_amount"
                                                required
                                            >
                                            <!-- pindahkan total ke data-total agar JS bebas dari sintaks Blade -->
                                            <button
                                                id="getTotalAmount"
                                                class="btn btn-primary"
                                                type="button"
                                                data-total="{{ \Cart::instance('sale')->total() }}"
                                            >
                                                <i class="bi bi-check-square"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">Note (If Needed)</label>
                                <textarea
                                    name="note"
                                    id="note"
                                    rows="5"
                                    class="form-control"
                                ></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Create Sale <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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

            // Tombol "Get Total" sekarang baca dari data-total
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
