@extends('layouts.app')

@section('title', 'POS')

@section('third_party_stylesheets')
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">POS</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
            </div>
            <div class="col-lg-7">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        {{-- KEMBALI KE SINTAKS BOOTSTRAP 4 --}}
                        <a class="nav-link active" id="new-product-tab" data-toggle="tab" href="#new-product" role="tab"
                            aria-controls="new-product" aria-selected="true">Produk Baru</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="second-product-tab" data-toggle="tab" href="#second-product" role="tab"
                            aria-controls="second-product" aria-selected="false">Produk Bekas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manual-item-tab" data-toggle="tab" href="#manual-item" role="tab"
                            aria-controls="manual-item" aria-selected="false">Jasa / Manual</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="new-product" role="tabpanel"
                        aria-labelledby="new-product-tab">
                        <div class="mt-3">
                            <livewire:search-product />
                            <livewire:pos.product-list :categories="$categories" />
                        </div>
                    </div>

                    <div class="tab-pane fade" id="second-product" role="tabpanel" aria-labelledby="second-product-tab">
                        <div class="mt-3">
                            <livewire:pos.product-list-second />
                        </div>
                    </div>

                    <div class="tab-pane fade" id="manual-item" role="tabpanel" aria-labelledby="manual-item-tab">
                        <div class="mt-3">
                            <livewire:pos.manual-item-form />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <livewire:pos.checkout :cart-instance="'sale'" />
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        function confirmReset() {
            Swal.fire({
                title: 'Anda Yakin?',
                text: "Semua item di keranjang akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('resetCart');
                    Swal.fire(
                        'Dihapus!',
                        'Keranjang Anda telah dikosongkan.',
                        'success'
                    )
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(e) {
                if (e.target.id === 'confirm-sale-btn') {
                    const form = document.getElementById('checkout-form');
                    const totalAmount = document.getElementById('total_amount').value;
                    const paidAmount = document.getElementById('paid_amount').value;

                    if (!paidAmount) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Received Amount tidak boleh kosong!',
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Transaksi',
                        html: `Anda akan menyelesaikan transaksi dengan total <strong>${totalAmount}</strong>.<br><br>Apakah Anda yakin?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            window.addEventListener('showCheckoutModal', event => {
                $('#checkoutModal').modal('show');
            });

            // Listener untuk notifikasi (jika Anda membutuhkannya)
            window.addEventListener('showSuccess', event => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: event.detail.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            window.addEventListener('showWarning', event => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: event.detail.message,
                });
            });

            window.addEventListener('showError', event => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: event.detail.message,
                });
            });

            $('#paid_amount').maskMoney({
                prefix: '{{ settings()->currency->symbol }}',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                allowZero: false,
            });

            $('#total_amount').maskMoney({
                prefix: '{{ settings()->currency->symbol }}',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
                allowZero: true,
            });

            $('#paid_amount').maskMoney('mask');
            $('#total_amount').maskMoney('mask');

            $('#checkout-form').submit(function() {
                const paid = $('#paid_amount').maskMoney('unmasked')[0];
                $('#paid_amount').val(paid);
                const total = $('#total_amount').maskMoney('unmasked')[0];
                $('#total_amount').val(total);
            });
        });
    </script>
@endpush
