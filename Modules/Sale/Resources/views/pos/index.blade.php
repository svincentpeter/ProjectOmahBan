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
                {{-- ✅ Bootstrap 4 Native Tabs --}}
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="new-product-tab" data-toggle="tab" href="#new-product" role="tab"
                            aria-controls="new-product" aria-selected="true">
                            <i class="bi bi-box-seam"></i> Produk Baru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="second-product-tab" data-toggle="tab" href="#second-product" role="tab"
                            aria-controls="second-product" aria-selected="false">
                            <i class="bi bi-arrow-repeat"></i> Produk Bekas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manual-item-tab" data-toggle="tab" href="#manual-item" role="tab"
                            aria-controls="manual-item" aria-selected="false">
                            <i class="bi bi-pencil-square"></i> Jasa / Manual
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    {{-- Tab 1: Produk Baru --}}
                    <div class="tab-pane fade show active" id="new-product" role="tabpanel"
                        aria-labelledby="new-product-tab">
                        <div class="mt-3">
                            <livewire:search-product />
                            <livewire:pos.product-list :categories="$categories" />
                        </div>
                    </div>

                    {{-- Tab 2: Produk Bekas --}}
                    <div class="tab-pane fade" id="second-product" role="tabpanel" aria-labelledby="second-product-tab">
                        <div class="mt-3">
                            <livewire:pos.product-list-second />
                        </div>
                    </div>

                    {{-- Tab 3: Jasa / Manual --}}
                    <div class="tab-pane fade" id="manual-item" role="tabpanel" aria-labelledby="manual-item-tab">
                        <div class="mt-3">
                            <livewire:pos.manual-item-form />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checkout Panel --}}
            <div class="col-lg-5">
                <livewire:pos.checkout :cart-instance="'sale'" />
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        // ✅ FIXED: Pakai 'livewire:initialized' bukan 'DOMContentLoaded'
        document.addEventListener('livewire:initialized', () => {
            console.log('✅ Livewire initialized on POS page');

            // Konfirmasi reset keranjang
            window.confirmReset = function() {
                Swal.fire({
                    title: 'Anda Yakin?',
                    text: 'Semua item di keranjang akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ✅ FIXED: Livewire 3 syntax
                        Livewire.dispatch('resetCart');
                    }
                });
            };

            // ✅ Listener untuk event Swal (sudah ada di main-js.blade.php secara global)
            // Tapi bisa ditambahkan lagi di sini jika perlu override
            Livewire.on('swal-success', (data) => {
                const msg = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: msg,
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });

            Livewire.on('swal-error', (data) => {
                const msg = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg,
                    confirmButtonText: 'OK'
                });
            });

            Livewire.on('swal-warning', (data) => {
                const msg = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: msg,
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });

            // ✅ Modal checkout (jika digunakan di masa depan)
            Livewire.on('showCheckoutModal', () => {
                if (typeof $ !== 'undefined' && $('#checkoutModal').length) {
                    $('#checkoutModal').modal('show');
                }
            });
        });

        // ✅ TAMBAHAN: jQuery ready untuk Bootstrap tabs (fallback)
        $(document).ready(function() {
            console.log('✅ jQuery ready - Bootstrap tabs initialized');
            
            // Pastikan Bootstrap tabs berfungsi
            $('#myTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endpush
