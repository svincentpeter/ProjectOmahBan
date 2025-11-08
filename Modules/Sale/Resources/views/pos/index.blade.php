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

            {{-- ================= Page Header Card ================= --}}
            <div class="col-12">
                <div class="card page-head-card shadow-sm mb-3">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="mb-3 mb-md-0">
                                <h4 class="mb-1 d-flex align-items-center">
                                    <i class="cil-cart mr-2 text-primary"></i>
                                    Point of Sale
                                </h4>
                                <div class="text-muted">
                                    Pilih <strong>Produk Baru</strong>, <strong>Produk Bekas</strong>, atau <strong>Jasa /
                                        Manual</strong> pada tab di bawah.
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <a href="{{ route('service-masters.index') }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="cil-settings mr-1"></i> Kelola Master Jasa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Left: Catalog Tabs ================= --}}
            <div class="col-lg-7">
                {{-- Tabs (Bootstrap 4) --}}
                <ul class="nav nav-tabs pos-tabs" id="myTab" role="tablist">
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

                            {{-- ===== SECTION A: Daftar Jasa (Master Data)
                   Catatan: kalau komponen service-list SUDAH ber-card sendiri (versi terbaru),
                   biarkan seperti ini. --}}
                            <livewire:pos.service-list />

                            {{-- ===== SECTION B: Input Manual (Non Master) --}}
                            <div class="card shadow-sm mt-4">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h6 class="mb-1 font-weight-bold d-flex align-items-center">
                                        <i class="cil-pencil mr-2 text-primary"></i>
                                        Input Manual (Item Tidak Ada di Master)
                                    </h6>
                                    <small class="text-muted">Gunakan untuk jasa/barang yang belum ada di master
                                        data</small>
                                </div>
                                <div class="card-body">
                                    <div class="pos-callout pos-callout--danger mb-4">
                                        <i class="bi bi-exclamation-triangle mr-1"></i>
                                        <strong>Perhatian:</strong> Setiap input manual akan dikirim sebagai notifikasi ke
                                        Owner untuk audit.
                                        Pastikan alasan diisi dengan jelas.
                                    </div>

                                    {{-- Komponen form manual (sudah kita rapikan & ada alasan wajib) --}}
                                    <livewire:pos.manual-item-form />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Right: Checkout Panel ================= --}}
            <div class="col-lg-5">
                @livewire('pos.checkout', ['cartInstance' => 'sale'])
            </div>

        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* ===== Header card ===== */
        .page-head-card {
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
            border: 1px solid #edf2f7;
        }

        /* ===== Tabs look & feel (rapi & clickable) ===== */
        .pos-tabs .nav-link {
            font-weight: 600;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: .6rem .9rem;
            transition: .2s;
        }

        .pos-tabs .nav-link:hover {
            background: #f8f9fa
        }

        .pos-tabs .nav-link.active {
            color: #4834DF;
            border-color: #dee2e6 #dee2e6 #fff;
            box-shadow: inset 0 -2px 0 #4834DF;
        }

        /* ===== Callouts ===== */
        .pos-callout {
            border-radius: 10px;
            padding: 12px 14px;
            border-left: 4px solid
        }

        .pos-callout--danger {
            border-color: #e55353;
            background: #ffecec
        }

        .pos-callout--warning {
            border-color: #f9b115;
            background: #fff7e6
        }

        .pos-callout--info {
            border-color: #39f;
            background: #f1f7ff
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        // Livewire init + helpers
        document.addEventListener('livewire:initialized', () => {
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
                }).then((r) => {
                    if (r.isConfirmed) Livewire.dispatch('resetCart');
                });
            };

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
                    text: msg
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

            Livewire.on('showCheckoutModal', () => {
                if (typeof $ !== 'undefined' && $('#checkoutModal').length) $('#checkoutModal').modal(
                    'show');
            });
        });

        // Fallback init tabs (Bootstrap 4)
        $(function() {
            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endpush
